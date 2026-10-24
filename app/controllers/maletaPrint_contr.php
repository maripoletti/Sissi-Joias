<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/maletas_model.php';

feature_run(function () {
    $user = feature_guard();
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') feature_json(['success' => false, 'message' => 'Método não permitido.'], 405);
    $target = $_SESSION['role'] === 2 ? feature_int($_GET['revendedora_id'] ?? null) : $user;
    $case = feature_int($_GET['case_id'] ?? null, 0);
    $document = (new Maletas_model())->documento($case, $target);
    $format = $_GET['formato'] ?? 'a4';
    if (!is_string($format)) throw new InvalidArgumentException('Formato inválido.');
    $pdf = MaletaPdf::gerar($document, $format);
    header('Content-Type: application/pdf');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');
    header('Content-Disposition: inline; filename="Maleta-' . $case . '-' . $format . '.pdf"');
    echo $pdf;
    exit;
});
