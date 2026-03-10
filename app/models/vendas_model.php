<?php
declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";

class vendas_model extends Dbh {
    public function delete_sales(int $id) {
        $pdo = $this->connect();

        try {
            $stmt = $pdo->prepare("UPDATE Sales_Orders SET Status = '0' WHERE OrderID = :id");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            http_response_code(500);
            exit;
        }
    }
    public function get_total_sales(int $userID, int $role) {

        $pdo = $this->connect();

        try {

            $query = "
            SELECT COUNT(*) AS total
            FROM Sales_Orders so
            JOIN Sales_Customers c ON so.CustomerID = c.CustomerID
            JOIN Sales_Employees e ON c.EmployeeID = e.EmployeeID
            WHERE so.Status = 1";

            if ($role !== 2) {
                $query .= " AND e.UserID = :uid";
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
            LEFT JOIN Sales_Customers c ON so.CustomerID = c.CustomerID
            LEFT JOIN Sales_Employees e ON c.EmployeeID = e.EmployeeID
            LEFT JOIN Sales_Products p ON so.ProductID = p.ProductID
            WHERE so.Status = '1'";

            if ($role !== 2) {
                $query .= " AND e.UserID = :uid";
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