<?php

declare(strict_types= 1);

class printerServices {
    public static function imprimir(string $ip, int $port, array $produtos) {
        $nome = $produtos["ProductName"];
        $price = number_format($produtos["Price"],2,",",".");
        $codigodebarras = $produtos["Barcode"];

        $zpl = "";
    }
    public static function testar($ip, $port=9100){

    $fp = @fsockopen($ip,$port,$errno,$errstr,2);

    if(!$fp){
        return "offline";
    }

    fclose($fp);
    return "online";
    }
}