<?php

declare(strict_types=1);

class orderValidator {

    public static function validate(array $cliente, string $pagamento, array $produto) {

        $errors = [];

        $clean = [
            "cliente" => [
                "nome" => trim($cliente["nome"] ?? ""),
                "cpf" => preg_replace('/\D/', '', $cliente["cpf"] ?? null)
            ],
            "pagamento" => trim($pagamento ?? ""),
            "produto" => null
        ];

        if ($clean["cliente"]["nome"] === "") {
            $errors["nome_empty"] = "Nome vazio.";
        }

        if (mb_strlen($clean["cliente"]["nome"]) > 100) {
            $errors["nome_big"] = "Nome muito grande.";
        }

        if (!empty($clean["cliente"]["cpf"]) && strlen($clean["cliente"]["cpf"]) !== 11) {
            $errors["cpf_invalid"] = "CPF inválido.";
        }

        $pagamentosValidos = ["Dinheiro","Cartão","Pix"];

        if (!in_array($clean["pagamento"], $pagamentosValidos, true)) {
            $errors["pagamento_invalid"] = "Pagamento inválido.";
        }

        $id = (int)($produto[0]["id"] ?? null);
        $quantidade = (int)($produto[0]["quantidade"] ?? null);
        $preco = (float)($produto[0]["preco"] ?? null);

        if (!is_int($id) || $id <= 0) {
            $errors["produto_invalid"] = "Produto inválido.";
        }

        if (!is_int($quantidade) || $quantidade < 1) {
            $errors["quantidade_invalid"] = "Quantidade inválida.";
        }

        if (!is_numeric($preco) || $preco <= 0) {
            $errors["preco_invalid"] = "Preço inválido.";
        }

        if (empty($errors)) {
            $clean["produto"] = [
                "id" => $id,
                "quantidade" => $quantidade,
                "preco" => (float)$preco
            ];
        }

        return [
            "data" => $clean,
            "errors" => $errors
        ];
    }
}