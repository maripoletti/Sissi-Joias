<?php

require_once __DIR__ . "/../../config/dbh.config.php";

class controledegastos_model extends Dbh {
    public function pegarMetais (string $texto) {
        $pdo = $this->connect();

        try {
            $query = "SELECT Name AS nome FROM Prod_Metals WHERE Name LIKE ? AND Status=1";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$texto]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }

    public function pegarFixos(array $intervalos, string $metal) {
        $pdo = $this->connect();

        try {
            $resultado = [];

            // CUSTO DE INSUMOS
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(InputCosts), 0)
                FROM Sales_Products
                WHERE Status = 1
                AND CreatedAt BETWEEN ? AND ?
            ");

            $stmt->execute([
                $intervalos["item-insumo"]["inicio"] . " 00:00:00",
                $intervalos["item-insumo"]["fim"] . " 23:59:59"
            ]);

            $resultado["insumos"] = (float) $stmt->fetchColumn();

            // ENTRADAS
            $stmt = $pdo->prepare("
                SELECT
                    COALESCE(SUM(StockQuantity), 0)
                FROM Sales_Products
                WHERE Status = 1
                AND CreatedAt BETWEEN ? AND ?
            ");

            $stmt->execute([
                $intervalos["item-entrada"]["inicio"] . " 00:00:00",
                $intervalos["item-entrada"]["fim"] . " 23:59:59"
            ]);

            $estoqueAtual = (int)$stmt->fetchColumn();

            $stmt = $pdo->prepare("
                SELECT
                    COALESCE(SUM(soi.Quantity), 0)
                FROM Sales_OrderItems soi
                INNER JOIN Sales_Orders so
                    ON so.OrderID = soi.OrderID
                WHERE so.Status = 1
                AND so.OrderDate BETWEEN ? AND ?
            ");

            $stmt->execute([
                $intervalos["item-entrada"]["inicio"] . " 00:00:00",
                $intervalos["item-entrada"]["fim"] . " 23:59:59"
            ]);

            $vendidas = (int)$stmt->fetchColumn();
            $resultado["entradas"] = $estoqueAtual + $vendidas;

            // CUSTO DE BANHO
            $sql = "
                SELECT COALESCE(
                    SUM(
                        COALESCE(sp.Weight, 0)
                        * COALESCE(pm.ValuePerGram, 0)
                        * COALESCE(sp.BathThickness, 0)
                        / 1000
                    ),
                    0
                )
                FROM Sales_Products sp
                INNER JOIN Prod_Metals pm
                    ON pm.MetalID = sp.BathMetal
                WHERE sp.Status = 1
                AND sp.CreatedAt BETWEEN ? AND ?
            ";

            $params = [
                $intervalos["item-banho"]["inicio"] . " 00:00:00",
                $intervalos["item-banho"]["fim"] . " 23:59:59"
            ];

            if ($metal !== "Todos") {
                $sql .= " AND pm.Name = ?";
                $params[] = $metal;
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $resultado["banho"] = (float) $stmt->fetchColumn();

            // SAÍDAS
            $stmt = $pdo->prepare("
                SELECT COALESCE(SUM(soi.Quantity), 0)
                FROM Sales_OrderItems soi
                INNER JOIN Sales_Orders so
                    ON so.OrderID = soi.OrderID
                WHERE so.Status = '1'
                AND so.OrderDate BETWEEN ? AND ?
            ");

            $stmt->execute([
                $intervalos["item-saida"]["inicio"] . " 00:00:00",
                $intervalos["item-saida"]["fim"] . " 23:59:59"
            ]);

            $resultado["saidas"] = (int) $stmt->fetchColumn();

            return $resultado;

        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }

    public function pegarGastos(array $intervalo) {
        $pdo = $this->connect();

        try {
            $query = 
                "SELECT
                    CostID AS id,
                    CostName AS nome,
                    CostPrice AS custo,
                    CostDate AS data
                FROM Costs
                WHERE Status = '1'
                AND CostDate BETWEEN ? AND ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$intervalo["inicio"], $intervalo["fim"]]);

            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }

    public function editarGasto(string $nome, float $preco, string $data, int $id) {
        $pdo = $this->connect();

        try {
            $query = "UPDATE Costs SET CostName=?, CostPrice=?, CostDate=? WHERE CostID=? AND Status='1'";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$nome, $preco, $data, $id]);

            return [
                "success" => true,
            ];
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
            return [
                "success" => false,
            ];
        }
    }
    public function adicionarGasto(string $nome, float $preco, string $data) {
        $pdo = $this->connect();

        try {
            $query = "INSERT INTO Costs (CostName, CostPrice, CostDate) VALUES (?,?,?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$nome, $preco, $data]);

            return [
                "success" => true,
            ];
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
            return [
                "success" => false,
            ];
        }
    }

    public function excluirGasto(int $id) {
        $pdo = $this->connect();

        try {
            $query = "UPDATE Costs SET Status='0' WHERE CostID=?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$id]);

            return [
                "success" => true,
            ];
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
            return [
                "success" => false,
            ];
        }
    }
}