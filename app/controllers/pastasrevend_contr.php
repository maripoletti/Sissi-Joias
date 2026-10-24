<?php
declare(strict_types=1);
require_once __DIR__ . '/../helpers/featureApi.php';
require_once __DIR__ . '/../models/pastasrevend_model.php';

feature_run(function () {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $read = in_array($path, ['/api/pastasrevendedoras', '/api/arquivos/baixar'], true);
    $expected = $read ? 'GET' : 'POST';
    if ($_SERVER['REQUEST_METHOD'] !== $expected) feature_json(['success' => false, 'message' => 'Método não permitido.'], 405);
    $adminOnly = in_array($path, ['/api/pastasrevendedoras', '/api/arquivos/upload', '/api/arquivos/excluir'], true);
    $user = feature_guard($adminOnly, !$read);
    $admin = $_SESSION['role'] === 2;
    $model = new Pastasrevend_model();
    if ($path === '/api/pastasrevendedoras') return $model->pastas();
    if ($path === '/api/arquivos/baixar') {
        $file = $model->baixar(feature_int($_GET['id'] ?? null), $admin ? null : $user);
        header('Content-Type: ' . $file['MimeType']);
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');
        header("Content-Disposition: attachment; filename=\"arquivo\"; filename*=UTF-8''" . rawurlencode($file['FileName']));
        echo $file['Content'];
        exit;
    }
    $input = $path === '/api/arquivos/upload' ? $_POST : feature_input();
    // O ID do navegador só é considerado para a administradora.
    $target = $admin ? feature_int($input['revendedora_id'] ?? null) : $user;
    if ($path === '/api/arquivos/listar') return $model->listar($target);
    if ($path === '/api/arquivos/upload') {
        if (!isset($_FILES['arquivo']) || !is_string($_FILES['arquivo']['name'])) {
            throw new InvalidArgumentException('Selecione um arquivo.');
        }
        return ['success' => true, 'id' => $model->upload($target, $_FILES['arquivo'])];
    }
    $id = feature_int($input['id'] ?? null);
    if ($path === '/api/arquivos/excluir') $model->excluir($id, $target);
    if ($path === '/api/arquivos/assinalar') {
        if (!is_bool($input['assinalado'] ?? null)) throw new InvalidArgumentException('Marcação inválida.');
        $model->assinalar($id, $target, $input['assinalado']);
    }
    return ['success' => true];
});
