<?php

class Dbh {
    
    protected function connect() {
        try {
            $dbusername = 'sissisemijoiase';
            $dbpassword = 'VPS11062010h!DB';

            $pdo = new PDO("mysql:host=localhost;dbname=sissisemijoiaseacessorios", $dbusername, $dbpassword, [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"]);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}