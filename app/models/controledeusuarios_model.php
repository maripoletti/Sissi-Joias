<?php

declare(strict_types= 1);
require_once __DIR__ . "/../../config/dbh.config.php";

class ControledeusuariosModel extends Dbh {
    public function switch_lvl($id, $nivel) {
        $pdo = $this->connect();

        try {
            $query = "UPDATE Temp_PendingUsers SET Level = :nivel WHERE PendUserID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":nivel", $nivel, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro na conexão: ". $e->getMessage();
        }
    }
    public function reject_user(int $tempid) {
        $pdo = $this->connect();

        $pdo->beginTransaction();

        try {

            $query = "SELECT * FROM Temp_PendingUsers WHERE PendUserID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $tempid, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$user) {
                throw new Exception("Usuário não encontrado");
            }

            $query = "SELECT UserID FROM Auth_Users WHERE Email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":email", $user["Email"]);
            $stmt->execute();
            $authUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if($authUser) {

                $userID = $authUser["UserID"];

                $query = "DELETE FROM Auth_UserRoles WHERE UserID = :userid";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":userid", $userID);
                $stmt->execute();

                $query = "DELETE FROM Sales_Employees WHERE UserID = :userid";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":userid", $userID);
                $stmt->execute();

                $query = "DELETE FROM Events WHERE UserID = :userid";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":userid", $userID);
                $stmt->execute();

                $query = "DELETE FROM Auth_Users WHERE UserID = :userid";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(":userid", $userID);
                $stmt->execute();
            }

            $query = "UPDATE Temp_PendingUsers 
                    SET Status = 'rejeitado' 
                    WHERE PendUserID = :userid";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $tempid, PDO::PARAM_INT);
            $stmt->execute();

            $pdo->commit();

        } catch (Exception $e) {
            $pdo->rollback();
            echo "Erro: " . $e->getMessage();
        }
    }
    public function create_user_from_pending(int $PendUserID) {
        $pdo = $this->connect();

        $pdo->beginTransaction();

        try {
            $query = "SELECT * FROM Temp_PendingUsers WHERE PendUserID = :id;";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $PendUserID);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!$user) {
                throw new Exception("Usuário não encontrado");
            };

            $query = "INSERT INTO Auth_Users (Pwd, Email) VALUES (:pwd, :email);";
            $stmt = $pdo->prepare($query);
            $stmt-> bindParam(":pwd", $user['Pwd']);
            $stmt-> bindParam(":email", $user['Email']);
            $stmt-> execute();

            $UserID = $pdo->lastInsertId();

            $query = "INSERT INTO Sales_Employees (UserID, FullName, Phone) VALUES (:userid, :fullname, :phone);";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $UserID);
            $stmt->bindParam(":fullname", $user['FullName']);
            $stmt->bindParam(":phone", $user['Phone']);
            $stmt-> execute();

            $query = "INSERT INTO Auth_UserRoles (UserID, RoleID) VALUES (:userid, 1)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $UserID);
            $stmt->execute();

            $query = "UPDATE Temp_PendingUsers SET Status = 'aprovado' WHERE PendUserID = :userid";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":userid", $PendUserID, PDO::PARAM_INT);
            $stmt->execute();

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollback();
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
    public function get_employees_by_status(string $status) {
        $pdo = $this->connect();

        try {
            $query = "SELECT
                PendUserID AS id,
                FullName AS nome,
                Email AS email,
                Phone AS telefone,
                Status,
                Level AS nivel
            FROM Temp_PendingUsers WHERE Status = :status ORDER BY PendUserID DESC";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":status", $status, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw $e;
        }
    }
    public function get_status_by_id(int $id) {
        $pdo = $this->connect();

        try {
            $query = "SELECT Status FROM Temp_PendingUsers WHERE PendUserID = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro na conexão: " . $e->getMessage();
        }
    }
}