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
            $stmt = $pdo->prepare('INSERT INTO Sales_Cases (Name, RequestKey, RequestHash) VALUES (?, ?, ?)');
            $stmt->execute([$data['nome_maleta'], $data['request_key'], $hash]);
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

    public function reagrupar(array $input): void {
        $items = $input['produtos'] ?? [];
        if (!is_array($items) || !$items || count($items) > 500) throw new InvalidArgumentException('Selecione os produtos.');
        $case = feature_int($input['case_id'] ?? 0, 0);
        $name = $input['nome_maleta'] ?? '';
        if (!$case && (!is_string($name) || trim($name) === '' || mb_strlen(trim($name)) > 100)) {
            throw new InvalidArgumentException('Informe o nome da maleta com até 100 caracteres.');
        }
        $pdo = $this->connect();
        $pdo->beginTransaction();
        try {
            if (!$case) {
                $stmt = $pdo->prepare('INSERT INTO Sales_Cases (Name) VALUES (?)');
                $stmt->execute([trim($name)]);
                $case = (int)$pdo->lastInsertId();
            } else {
                $stmt = $pdo->prepare('SELECT CaseID FROM Sales_Cases WHERE CaseID = ? FOR UPDATE');
                $stmt->execute([$case]);
                if (!$stmt->fetchColumn()) throw new InvalidArgumentException('Maleta não encontrada.');
            }
            $seen = [];
            foreach ($items as $item) {
                $product = feature_int($item['produto_id'] ?? null);
                $user = feature_int($item['revendedor_id'] ?? null);
                $oldCase = feature_int($item['case_id_atual'] ?? 0, 0);
                $key = "$user-$product-$oldCase";
                if (isset($seen[$key])) throw new InvalidArgumentException('Produto repetido.');
                $seen[$key] = true;
                if ($oldCase === $case) continue;
                $stmt = $pdo->prepare('SELECT * FROM Sales_EmployeeProducts WHERE UserID = ? AND ProductID = ? AND CaseID = ? FOR UPDATE');
                $stmt->execute([$user, $product, $oldCase]);
                $source = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$source || (int)$source['UsableStock'] !== feature_int($item['quantidade'] ?? null, 0, 10000)) {
                    throw new DomainException('A quantidade mudou. Atualize a lista antes de mover os produtos.');
                }
                $stmt->execute([$user, $product, $case]);
                $destination = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($destination) {
                    foreach (['OriginalPrice', 'UnitPrice', 'CouponPercent'] as $field) {
                        if ((string)$source[$field] !== (string)$destination[$field]) {
                            throw new DomainException('Este produto tem preços diferentes nas maletas. Escolha outra maleta para preservar os descontos.');
                        }
                    }
                    $stmt = $pdo->prepare('UPDATE Sales_EmployeeProducts SET UsableStock = UsableStock + ? WHERE UserID = ? AND ProductID = ? AND CaseID = ?');
                    $stmt->execute([$source['UsableStock'], $user, $product, $case]);
                    $stmt = $pdo->prepare('DELETE FROM Sales_EmployeeProducts WHERE UserID = ? AND ProductID = ? AND CaseID = ?');
                    $stmt->execute([$user, $product, $oldCase]);
                } else {
                    $stmt = $pdo->prepare('UPDATE Sales_EmployeeProducts SET CaseID = ? WHERE UserID = ? AND ProductID = ? AND CaseID = ?');
                    $stmt->execute([$case, $user, $product, $oldCase]);
                }
                foreach ([$oldCase, $case] as $affected) {
                    if (!$affected) continue;
                    $stmt = $pdo->prepare('INSERT INTO Sales_CasesProducts (CaseID, ProductID, Quantity)
                        SELECT ?, ?, COALESCE(SUM(UsableStock), 0) FROM Sales_EmployeeProducts WHERE CaseID = ? AND ProductID = ?
                        ON DUPLICATE KEY UPDATE Quantity = VALUES(Quantity)');
                    $stmt->execute([$affected, $product, $affected, $product]);
                }
            }
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }
}
