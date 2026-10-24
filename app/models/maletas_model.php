<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/dbh.config.php';
require_once __DIR__ . '/../helpers/featureApi.php';
require_once __DIR__ . '/../services/maletaPdf.php';
require_once __DIR__ . '/pastasrevend_model.php';

class Maletas_model extends Dbh {
    public static function validarEnvio(array $input): array {
        $name = $input['nome_maleta'] ?? '';
        if (!is_string($name) || trim($name) === '' || mb_strlen(trim($name)) > 100) {
            throw new InvalidArgumentException('Informe o nome da maleta com até 100 caracteres.');
        }
        $key = $input['request_key'] ?? '';
        if (!is_string($key) || !preg_match('/^[a-f0-9]{64}$/D', $key)) {
            throw new InvalidArgumentException('Identificação do envio inválida. Atualize a página.');
        }
        $save = $input['salvar_a4'] ?? false;
        if (!is_bool($save)) throw new InvalidArgumentException('Opção de impressão inválida.');
        $products = $input['produtos'] ?? null;
        if (!is_array($products) || !$products || count($products) > 500) {
            throw new InvalidArgumentException('Selecione de 1 a 500 produtos.');
        }
        $items = [];
        foreach ($products as $product) {
            if (!is_array($product)) throw new InvalidArgumentException('Produto inválido.');
            $id = feature_int($product['produto_id'] ?? null);
            if (isset($items[$id])) throw new InvalidArgumentException('Produto repetido no envio.');
            if (!empty($product['cupom'])) throw new InvalidArgumentException('Selecione um cupom cadastrado da revendedora.');
            $coupon = $product['cupom_id'] ?? null;
            $items[$id] = ['produto_id' => $id, 'quantidade' => feature_int($product['quantidade'] ?? null, 1, 10000),
                'cupom_id' => $coupon === null ? null : feature_int($coupon)];
        }
        ksort($items);
        return ['revendedora_id' => feature_int($input['revendedora_id'] ?? null), 'nome_maleta' => trim($name),
            'salvar_a4' => $save, 'produtos' => array_values($items), 'request_key' => $key];
    }

    public function enviar(array $input): array {
        $data = self::validarEnvio($input);
        $hashData = $data;
        unset($hashData['request_key']);
        $hash = hash('sha256', json_encode($hashData, JSON_THROW_ON_ERROR));
        $pdo = $this->connect();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT FullName FROM Sales_Employees WHERE UserID = ? FOR UPDATE');
            $stmt->execute([$data['revendedora_id']]);
            $employee = $stmt->fetchColumn();
            if ($employee === false) throw new InvalidArgumentException('Revendedora não encontrada.');
            $stmt = $pdo->prepare('SELECT CaseID, RequestHash FROM Sales_Cases WHERE RequestKey = ? FOR UPDATE');
            $stmt->execute([$data['request_key']]);
            $previous = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($previous) {
                if (!hash_equals($previous['RequestHash'], $hash)) throw new DomainException('Este envio já foi processado com outros dados.');
                $pdo->commit();
                return ['success' => true, 'case_id' => (int)$previous['CaseID'], 'mensagem' => 'Maleta já enviada.'];
            }
            $stmt = $pdo->prepare('INSERT INTO Sales_Cases (Name, UserID, RequestKey, RequestHash) VALUES (?, ?, ?, ?)');
            $stmt->execute([$data['nome_maleta'], $data['revendedora_id'], $data['request_key'], $hash]);
            $case = (int)$pdo->lastInsertId();
            $document = ['nome' => $data['nome_maleta'], 'revendedora' => $employee, 'data' => date('d/m/Y H:i'), 'itens' => []];
            foreach ($data['produtos'] as $item) {
                $id = $item['produto_id'];
                $quantity = $item['quantidade'];
                $stmt = $pdo->prepare('SELECT ProductName, Price, StockQuantity FROM Sales_Products WHERE ProductID = ? AND Status = 1 FOR UPDATE');
                $stmt->execute([$id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$product) throw new DomainException('Um produto não está mais disponível. Atualize a lista.');
                // O estoque físico já inclui as peças que estão com as revendedoras.
                $stmt = $pdo->prepare('SELECT UsableStock FROM Sales_EmployeeProducts WHERE ProductID = ? FOR UPDATE');
                $stmt->execute([$id]);
                $used = array_sum($stmt->fetchAll(PDO::FETCH_COLUMN));
                if ($quantity > (int)$product['StockQuantity'] - $used) {
                    throw new DomainException('Estoque disponível insuficiente para ' . $product['ProductName'] . '.');
                }
                $percent = 0;
                if ($item['cupom_id'] !== null) {
                    $stmt = $pdo->prepare('SELECT DiscountPercent, Quantity FROM Sales_EmployeeCoupons
                        WHERE CouponID = ? AND UserID = ? FOR UPDATE');
                    $stmt->execute([$item['cupom_id'], $data['revendedora_id']]);
                    $coupon = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$coupon || (int)$coupon['Quantity'] < $quantity) {
                        throw new DomainException('Cupons insuficientes. Cada unidade do produto utiliza um cupom.');
                    }
                    $percent = (int)$coupon['DiscountPercent'];
                    $stmt = $pdo->prepare('UPDATE Sales_EmployeeCoupons SET Quantity = Quantity - ? WHERE CouponID = ? AND UserID = ? AND Quantity >= ?');
                    $stmt->execute([$quantity, $item['cupom_id'], $data['revendedora_id'], $quantity]);
                    if (!$stmt->rowCount()) throw new DomainException('O saldo de cupons mudou. Atualize a página.');
                }
                $original = MaletaPdf::centavos((string)$product['Price']);
                $final = MaletaPdf::desconto($original, $percent);
                $originalPrice = number_format($original / 100, 2, '.', '');
                $finalPrice = number_format($final / 100, 2, '.', '');
                $stmt = $pdo->prepare('INSERT INTO Sales_CasesProducts (CaseID, ProductID, Quantity) VALUES (?, ?, ?)');
                $stmt->execute([$case, $id, $quantity]);
                $stmt = $pdo->prepare('INSERT INTO Sales_EmployeeProducts
                    (UserID, ProductID, UsableStock, CaseID, OriginalPrice, UnitPrice, CouponPercent) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$data['revendedora_id'], $id, $quantity, $case, $originalPrice, $finalPrice, $percent]);
                if ($percent > 0) {
                    $stmt = $pdo->prepare('INSERT INTO Sales_CouponUses
                        (CouponID, UserID, CaseID, ProductID, ProductName, Quantity, DiscountPercent, OriginalPrice, FinalPrice)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$item['cupom_id'], $data['revendedora_id'], $case, $id, $product['ProductName'], $quantity, $percent, $originalPrice, $finalPrice]);
                }
                $document['itens'][] = ['produto' => $product['ProductName'], 'quantidade' => $quantity,
                    'original' => $originalPrice, 'final' => $finalPrice, 'desconto' => $percent];
            }
            if ($data['salvar_a4']) {
                Pastasrevend_model::salvar($pdo, $data['revendedora_id'], 'Maleta-' . $case . '-A4.pdf',
                    'application/pdf', MaletaPdf::gerar($document), $case);
            }
            $pdo->commit();
            return ['success' => true, 'case_id' => $case, 'mensagem' => 'Maleta enviada com sucesso.'];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public function documento(int $case, int $user): array {
        $pdo = $this->connect();
        $stmt = $pdo->prepare('SELECT p.ProductName AS produto, e.FullName AS revendedora, ep.UsableStock AS quantidade,
            COALESCE(ep.OriginalPrice, p.Price) AS original, COALESCE(ep.UnitPrice, p.Price) AS final,
            ep.CouponPercent AS desconto, ep.SendAt AS data, COALESCE(c.Name, "Sem maleta") AS nome
            FROM Sales_EmployeeProducts ep JOIN Sales_Products p ON p.ProductID = ep.ProductID
            JOIN Sales_Employees e ON e.UserID = ep.UserID LEFT JOIN Sales_Cases c ON c.CaseID = ep.CaseID
            WHERE ep.CaseID = ? AND ep.UserID = ? AND ep.UsableStock > 0 ORDER BY p.ProductName');
        $stmt->execute([$case, $user]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$items) throw new InvalidArgumentException('A maleta não possui produtos disponíveis para impressão.');
        return ['nome' => $items[0]['nome'], 'revendedora' => $items[0]['revendedora'],
            'data' => date('d/m/Y H:i', strtotime($items[0]['data'])), 'itens' => $items];
    }

    public function editarQuantidade(int $user, int $product, int $case, int $quantity): void {
        $pdo = $this->connect();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('SELECT StockQuantity FROM Sales_Products WHERE ProductID = ? FOR UPDATE');
            $stmt->execute([$product]);
            $stock = $stmt->fetchColumn();
            $stmt = $pdo->prepare('SELECT UserID, CaseID, UsableStock, CouponPercent FROM Sales_EmployeeProducts WHERE ProductID = ? FOR UPDATE');
            $stmt->execute([$product]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $current = null;
            $used = 0;
            foreach ($rows as $row) {
                $used += (int)$row['UsableStock'];
                if ((int)$row['UserID'] === $user && (int)$row['CaseID'] === $case) $current = $row;
            }
            if (!$current) throw new InvalidArgumentException('Produto não encontrado nesta maleta.');
            if ($quantity > (int)$current['UsableStock'] && (int)$current['CouponPercent'] > 0) {
                throw new DomainException('Para acrescentar peças com cupom, faça um novo envio para consumir os cupons correspondentes.');
            }
            if ($stock === false || $used - (int)$current['UsableStock'] + $quantity > (int)$stock) {
                throw new DomainException('Estoque disponível insuficiente.');
            }
            $stmt = $pdo->prepare('UPDATE Sales_EmployeeProducts SET UsableStock = ? WHERE UserID = ? AND ProductID = ? AND CaseID = ?');
            $stmt->execute([$quantity, $user, $product, $case]);
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public function listar(?int $user = null): array {
        $query = 'SELECT CaseID, Name AS CaseName, UserID FROM Sales_Cases';
        if ($user !== null) $query .= ' WHERE UserID = ? OR UserID IS NULL';
        $query .= ' ORDER BY UserID IS NULL, Name, CaseID';
        $stmt = $this->connect()->prepare($query);
        $stmt->execute($user === null ? [] : [$user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function validarMovimentacao(array $input): array {
        $items = $input['produtos'] ?? [];
        if (!is_array($items) || !$items || count($items) > 500) throw new InvalidArgumentException('Selecione de 1 a 500 produtos.');
        $case = feature_int($input['case_id'] ?? 0, 0);
        $user = feature_int($input['revendedora_destino_id'] ?? null);
        $name = $input['nome_maleta'] ?? '';
        if (!$case && (!is_string($name) || trim($name) === '' || mb_strlen(trim($name)) > 100)) {
            throw new InvalidArgumentException('Informe o nome da maleta com até 100 caracteres.');
        }
        $normalized = [];
        foreach ($items as $item) {
            if (!is_array($item)) throw new InvalidArgumentException('Produto inválido.');
            $product = feature_int($item['produto_id'] ?? null);
            $sourceUser = feature_int($item['revendedor_id'] ?? null);
            $sourceCase = feature_int($item['case_id_atual'] ?? 0, 0);
            $key = "$sourceUser-$product-$sourceCase";
            if (isset($normalized[$key])) throw new InvalidArgumentException('Produto repetido.');
            $normalized[$key] = ['produto_id' => $product, 'revendedor_id' => $sourceUser,
                'case_id_atual' => $sourceCase, 'quantidade' => feature_int($item['quantidade'] ?? null, 1)];
        }
        return ['case_id' => $case, 'revendedora_destino_id' => $user,
            'nome_maleta' => $case ? '' : trim($name), 'produtos' => array_values($normalized)];
    }

    private function atualizarQuantidadeMaleta(PDO $pdo, int $case, int $product): void {
        if (!$case) return;
        $stmt = $pdo->prepare('INSERT INTO Sales_CasesProducts (CaseID, ProductID, Quantity)
            SELECT ?, ?, COALESCE(SUM(UsableStock), 0) FROM Sales_EmployeeProducts WHERE CaseID = ? AND ProductID = ?
            ON DUPLICATE KEY UPDATE Quantity = VALUES(Quantity)');
        $stmt->execute([$case, $product, $case, $product]);
    }

    public function reagrupar(array $input): void {
        $data = self::validarMovimentacao($input);
        $items = $data['produtos'];
        $case = $data['case_id'];
        $targetUser = $data['revendedora_destino_id'];
        $pdo = $this->connect();
        $pdo->beginTransaction();
        try {
            // Mesma ordem de travas do envio: revendedoras, produtos e seus vínculos.
            $users = array_unique(array_merge([$targetUser], array_column($items, 'revendedor_id')));
            sort($users, SORT_NUMERIC);
            $stmt = $pdo->prepare('SELECT UserID FROM Sales_Employees WHERE UserID = ? FOR UPDATE');
            foreach ($users as $user) {
                $stmt->execute([$user]);
                if (!$stmt->fetchColumn()) throw new InvalidArgumentException('Revendedora não encontrada.');
            }
            $products = array_unique(array_column($items, 'produto_id'));
            sort($products, SORT_NUMERIC);
            $stmt = $pdo->prepare('SELECT ProductID FROM Sales_Products WHERE ProductID = ? FOR UPDATE');
            foreach ($products as $product) {
                $stmt->execute([$product]);
                if (!$stmt->fetchColumn()) throw new InvalidArgumentException('Produto não encontrado.');
            }
            $cases = array_unique(array_merge([$case], array_column($items, 'case_id_atual')));
            sort($cases, SORT_NUMERIC);
            $owners = [];
            $stmt = $pdo->prepare('SELECT UserID FROM Sales_Cases WHERE CaseID = ? FOR UPDATE');
            foreach ($cases as $id) {
                if (!$id) continue;
                $stmt->execute([$id]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$row) throw new InvalidArgumentException('Maleta não encontrada.');
                $owners[$id] = $row['UserID'] === null ? null : (int)$row['UserID'];
            }
            if (!$case) {
                $stmt = $pdo->prepare('INSERT INTO Sales_Cases (Name, UserID) VALUES (?, ?)');
                $stmt->execute([$data['nome_maleta'], $targetUser]);
                $case = (int)$pdo->lastInsertId();
            } elseif ($owners[$case] === null) {
                // Permite atribuir uma maleta histórica vazia cuja dona não pôde ser inferida.
                $stmt = $pdo->prepare('UPDATE Sales_Cases SET UserID = ? WHERE CaseID = ? AND UserID IS NULL');
                $stmt->execute([$targetUser, $case]);
            } elseif ($owners[$case] !== $targetUser) {
                throw new DomainException('A maleta escolhida pertence a outra revendedora. Atualize o destino.');
            }
            foreach ($items as $item) {
                $product = $item['produto_id'];
                $user = $item['revendedor_id'];
                $oldCase = $item['case_id_atual'];
                if ($oldCase && ($owners[$oldCase] ?? null) !== $user) {
                    throw new DomainException('A maleta de origem não pertence à revendedora informada. Atualize a lista.');
                }
                $stmt = $pdo->prepare('SELECT ep.*, p.Price AS CatalogPrice FROM Sales_EmployeeProducts ep
                    JOIN Sales_Products p ON p.ProductID = ep.ProductID
                    WHERE ep.UserID = ? AND ep.ProductID = ? AND ep.CaseID = ? FOR UPDATE');
                $stmt->execute([$user, $product, $oldCase]);
                $source = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$source || (int)$source['UsableStock'] !== $item['quantidade']) {
                    throw new DomainException('A quantidade mudou. Atualize a lista antes de mover os produtos.');
                }
                if ($oldCase === $case && $user === $targetUser) continue;
                // Congela também os preços dos itens antigos que ainda usavam o catálogo.
                $original = $source['OriginalPrice'] ?? $source['CatalogPrice'];
                $price = $source['UnitPrice'] ?? $source['CatalogPrice'];
                $stmt->execute([$targetUser, $product, $case]);
                $destination = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($destination) {
                    if (MaletaPdf::centavos((string)$original) !== MaletaPdf::centavos((string)($destination['OriginalPrice'] ?? $destination['CatalogPrice']))
                        || MaletaPdf::centavos((string)$price) !== MaletaPdf::centavos((string)($destination['UnitPrice'] ?? $destination['CatalogPrice']))
                        || (int)$source['CouponPercent'] !== (int)$destination['CouponPercent']) {
                        throw new DomainException('Este produto tem preços ou cupons diferentes no destino. Escolha outra maleta para preservar os valores.');
                    }
                    $stmt = $pdo->prepare('UPDATE Sales_EmployeeProducts SET UsableStock = UsableStock + ?, OriginalPrice = ?, UnitPrice = ?
                        WHERE UserID = ? AND ProductID = ? AND CaseID = ?');
                    $stmt->execute([$source['UsableStock'], $original, $price, $targetUser, $product, $case]);
                    $stmt = $pdo->prepare('DELETE FROM Sales_EmployeeProducts WHERE UserID = ? AND ProductID = ? AND CaseID = ?');
                    $stmt->execute([$user, $product, $oldCase]);
                } else {
                    $stmt = $pdo->prepare('UPDATE Sales_EmployeeProducts SET UserID = ?, CaseID = ?, OriginalPrice = ?, UnitPrice = ?
                        WHERE UserID = ? AND ProductID = ? AND CaseID = ?');
                    $stmt->execute([$targetUser, $case, $original, $price, $user, $product, $oldCase]);
                }
                $this->atualizarQuantidadeMaleta($pdo, $oldCase, $product);
                $this->atualizarQuantidadeMaleta($pdo, $case, $product);
            }
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }
}
