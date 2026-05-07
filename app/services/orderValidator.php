<?php

declare(strict_types=1);

class orderValidator {

    public static function validate(array $cliente, string $pagamento, array $produtos) {

        $errors = [];

        $clean = [
            "cliente" => [
                "nome" => trim($cliente["nome"] ?? ""),
                "cpf" => preg_replace('/\D/', '', $cliente["cpf"] ?? null)
            ],
            "pagamento" => trim($pagamento ?? ""),
            "produtos" => []
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

        $pagamentosValidos = ["Dinheiro","Débito", "Crédito", "Pix"];

        if (!in_array($clean["pagamento"], $pagamentosValidos, true)) {
            $errors["pagamento_invalid"] = "Pagamento inválido.";
        }

        if (empty($produtos) || !is_array($produtos)) {
            $errors["produto_empty"] = "Nenhum produto enviado.";
        } else {
            foreach ($produtos as $i => $p) {

                $id = (int)($p["id"] ?? 0);
                $quantidade = (int)($p["quantidade"] ?? 0);
                $preco = (float)($p["preco"] ?? 0);

                if ($id <= 0) {
                    $errors["produto_{$i}_id"] = "Produto inválido.";
                }

                if ($quantidade < 1) {
                    $errors["produto_{$i}_quantidade"] = "Quantidade inválida.";
                }

                if ($preco <= 0) {
                    $errors["produto_{$i}_preco"] = "Preço inválido.";
                }

                $clean["produtos"][] = [
                    "id" => $id,
                    "quantidade" => $quantidade,
                    "preco" => $preco
                ];
            }
        }

        return [
            "data" => $clean,
            "errors" => $errors
        ];
    }
}