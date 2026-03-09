<?php
declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";

class vendas_model extends Dbh {
    public function get_total_sales(int $userID, int $role) {

        $pdo = $this->connect();

        try {

            $query = "
            SELECT COUNT(*) AS total
            FROM Sales_Orders so
            JOIN Sales_Customers c ON so.CustomerID = c.CustomerID
            JOIN Sales_Employees e ON c.EmployeeID = e.EmployeeID
            ";

            if ($role !== 2) {
                $query .= " WHERE e.UserID = :uid";
            }

            $stmt = $pdo->prepare($query);

            if ($role !== 2) {
                $stmt->bindValue(':uid', $userID, PDO::PARAM_INT);
            }

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
    public function get_sales(int $userID, int $role, int $page, int $limit) {

        $pdo = $this->connect();
        $offset = $page * $limit;

        try {

            $query = "
            SELECT so.OrderID, p.ProductName, so.Quantity, so.Sales, so.OrderDate,
                   e.EmployeeID, e.FullName AS EmployeeName,
                   c.FullName AS ClienteName, so.PaymentMethod
            FROM Sales_Orders so
            JOIN Sales_Customers c ON so.CustomerID = c.CustomerID
            JOIN Sales_Employees e ON c.EmployeeID = e.EmployeeID
            JOIN Sales_Products p ON so.ProductID = p.ProductID
            ";

            if ($role !== 2) {
                $query .= " WHERE e.UserID = :uid";
            }

            $query .= " ORDER BY so.OrderDate DESC LIMIT :limit OFFSET :offset";

            $stmt = $pdo->prepare($query);

            if ($role !== 2) {
                $stmt->bindValue(':uid', $userID, PDO::PARAM_INT);
            }

            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}