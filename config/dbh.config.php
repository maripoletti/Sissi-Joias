<?php

$host = 'localhost';
$dbname = 'sissisemijoiaseacessorios';
$dbusername = 'root';//'sissisemijoiase';
$dbpassword = '';//'VPS11062010h!DB';

/////////////////////////////
//MUDAR NOME DO BANCO ANTES DE SUBIR
/////////////////////////////

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}