<?php
declare(strict_types=1);

require_once __DIR__ . "/../models/categorias_model.php";
header("Content-Type: application/json");

class categorias_contr {

    private Categorias_model $db;

    public function __construct() {
        $this->db = new Categorias_model();
    }

    public function listar_categorias() {
        echo json_encode(
            $this->db->listar_categorias(),
            JSON_UNESCAPED_UNICODE
        );
    }

    public function adicionar_categoria() {
        $nome = trim($_POST["nome"] ?? "");

        if ($nome === "") {
            http_response_code(400);
            exit;
        }

        $this->db->adicionar_categoria($nome);
    }

    public function editar_categoria() {
        $id = (int)($_POST["id"] ?? 0);
        $nome = trim($_POST["nome"] ?? "");

        if ($id <= 0 || $nome === "") {
            http_response_code(400);
            exit;
        }

        $this->db->editar_categoria($id, $nome);
    }

    public function remover_categoria() {
        $id = (int)($_POST["id"] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            exit;
        }

        $this->db->remover_categoria($id);
    }
}