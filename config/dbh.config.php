<?php

/////////////////////////////
//MUDAR NOME DO BANCO ANTES DE SUBIR
/////////////////////////////

class Dbh {
    
    protected function connect() {
        try {
            $dbusername = 'root';//'sissisemijoiase';
            $dbpassword = '';//'VPS11062010h!DB';

            $pdo = new PDO("mysql:host=localhost;dbname=sissisemijoiaseacessorios", $dbusername, $dbpassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}