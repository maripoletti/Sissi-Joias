<?php

class Dbh {
    
    protected function connect() {
        try {
            $dbusername = 'root';
            $dbpassword = '';

            $pdo = new PDO("mysql:host=127.0.0.1;dbname=sissisemijoiaseacessorios", $dbusername, $dbpassword, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"]);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}