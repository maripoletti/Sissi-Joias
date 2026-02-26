<?php

declare(strict_types= 1);

class User_model extends Dbh {

    public function find_role_by_id(int $id) {
        $pdo = $this->connect();

        $query = "SELECT RoleID FROM Auth_UserRoles WHERE UserID = :userid"; 
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":userid", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}