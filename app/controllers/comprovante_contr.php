<?php

require_once '../app/models/comprovante_model.php';

$id = $_GET['id'] ?? null;

$db = new comprovante_model();

$venda = $db->buscar_venda($id);

require_once '../app/views/comprovante_view.php';