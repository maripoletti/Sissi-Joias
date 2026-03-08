<?php

declare(strict_types= 1);

class printValidator {
    public static function validate(string $nome, null|string $conexao, null|string $ip, int $porta, null|string $marca, null|string $modelo, null|string $localizacao, null|string $status, null|string $tipo) {
        $errors = [];
        $clean = [
            "nome"=> trim($nome),
            "tipo"=> $tipo,
            "conexao"=> $conexao,
            "ip"=> trim($ip),
            "porta"=> $porta,
            "localizacao"=> trim($localizacao),
            "marca"=> trim($marca),
            "modelo"=> trim($modelo),
            "status"=> $status
        ];

        if(mb_strlen($nome) > 70 || mb_strlen($marca) > 50 || mb_strlen($modelo) > 50 || mb_strlen($ip) > 15 || mb_strlen($localizacao) > 70) {
            $errors['nomes_grandes'] = "Limite de caracteres excedido.";
        }

        if($porta < 0 || $porta > 65535) {
            $errors["porta_errada"] = "Porta errada";
        }

        return [
            "errors"=> $errors,
            "data"=> $clean
        ];
    }
}