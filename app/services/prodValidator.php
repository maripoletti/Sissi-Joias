<?php

declare(strict_types= 1);

class prodValidator {
    public static function validate_add(
        string $name,
        array $tags,
        float $price,
        int $stock,
        null|string $photo,
        string $tamanho,
        string $cor,
        string $peso_banho,
        string $milesimos_banho
    ) {
        $errors = [];
        $clean = [
            'name' => trim($name ?? ''),
            'tags' => array_map(fn($v) => mb_strtolower($v, 'UTF-8'), $tags) ?? [],
            'price' => abs($price ?? 0),
            'stock' => is_int(abs($stock ?? 0)),
            'photo' => $photo,
            'tamanho' => trim($tamanho),
            'cor' => trim($cor),
            'peso_banho' => trim($peso_banho),
            'milesimos_banho' => trim($milesimos_banho)
        ];

        if(empty($clean['name']) || $clean['price'] <= 0) {
            $errors['needed_fields_empty'] = 'Existe algum campo vazio.';
        }
        if (mb_strlen($clean['name']) >= 100) {
            $errors['big_name'] = 'Limite máximo de caracteres excedido!';
        }
        if (!is_float($clean['price'])) {
            $errors['price_not_number'] = 'Algo deu errado.';
        }

        return [
            'data' => $clean,
            'errors'=> $errors
        ];
    }
    public static function validate_update(
        int $id,
        string $name,
        float $price,
        int $stock,
        null|string $photo,
        string $tamanho,
        string $cor,
        string $peso_banho,
        string $milesimos_banho
    ) {
        $errors = [];
        $clean = [
            'id' => $id,
            'name' => trim($name),
            'price' => abs($price),
            'stock' => abs($stock),
            'photo' => $photo,
            'tamanho' => trim($tamanho),
            'cor' => trim($cor),
            'peso_banho' => trim($peso_banho),
            'milesimos_banho' => trim($milesimos_banho)
        ];
        if (empty($clean['id'])) {
            $errors['product_wrong'] = 'Algo deu errado.';
        }
        if (!is_float($clean['price'])) {
            $errors['price_not_number'] = 'Algo deu errado.';
        }
        if (empty($clean['name']) || empty($clean['price'])) {
            $errors['name_empty'] = 'Preencha todos os campos.';
        }
        if ($clean['stock'] < 0 || $clean['price'] < 0) {
            $errors['invalid_values'] = 'Valores inválidos.';
        }
        if (mb_strlen($clean['name']) >= 100) {
            $errors['big_name'] = 'Limite máximo de caracteres excedido!';
        }
        
        return [
            'errors' => $errors,
            'data'=> $clean
        ];
    }
    public static function validate_get(
        string $text,
        string|array $tags,
        string $price,
        string $sort,
        int $limit,
        int $page,
        string $tamanho,
        string $cor,
        string $peso_banho,
        string $milesimos_banho
    ) {
        $data = [];

        switch ($price) {
            case "0-50":
                $min = 0;
                $max = 50;
                break;
            case "50-100":
                $min = 50;
                $max = 100;
                break;
            case "100-200":
                $min = 100;
                $max = 200;
                break;
            case "200+":
                $min = 200;
                $max = null;
                break;
            default:
                $min = null;
                $max = null;
        }

        $data = [
            'text' => $text,
            'tags'=> $tags,
            'sort' => $sort,
            'min'=> $min,
            'max'=> $max,
            'limit' => $limit,
            'page'=> $page,
            'tamanho' => trim($tamanho),
            'cor' => trim($cor),
            'peso_banho' => trim($peso_banho),
            'milesimos_banho' => trim($milesimos_banho)
        ];

        return $data;
    }
}