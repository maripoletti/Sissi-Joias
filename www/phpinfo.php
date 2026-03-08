<?php

echo 'PHP binary: ' . PHP_BINARY . '<br>';
echo 'PHP version: ' . PHP_VERSION . '<br>';
echo 'Loaded ini: ' . php_ini_loaded_file() . '<br>';
echo 'Scanned ini: ' . php_ini_scanned_files() . '<br>';
echo 'extension_dir: ' . ini_get('extension_dir') . '<br>';
echo 'pdo_mysql loaded: ';
var_dump(extension_loaded('pdo_mysql'));
echo 'MYSQL_ATTR_INIT_COMMAND exists: ';
var_dump(defined('PDO::MYSQL_ATTR_INIT_COMMAND'));