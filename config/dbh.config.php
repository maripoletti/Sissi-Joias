<?php

$host = 'localhost';
$dbname = 'sissisemijoiaseacessorios';
$dbusername = 'root';//'sissisemijoiase';
$dbpassword = '';//'VPS11062010h!DB';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}