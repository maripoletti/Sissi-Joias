<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/user_model.php';

function feature_json(mixed $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    exit;
}

function feature_guard(bool $admin = false, bool $mutation = false): int {
    $user = (int)($_SESSION['user_id'] ?? 0);
    if (!$user) feature_json(['success' => false, 'message' => 'Entre na sua conta.'], 401);
    $role = (new user_model())->find_role_by_id($user);
    $_SESSION['role'] = (int)($role['RoleID'] ?? 0);
    if (!in_array($_SESSION['role'], [1, 2], true) || ($admin && $_SESSION['role'] !== 2)) {
        feature_json(['success' => false, 'message' => 'Acesso não permitido.'], 403);
    }
    if ($mutation && (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''))) {
        feature_json(['success' => false, 'message' => 'Sessão expirada. Atualize a página.'], 403);
    }
    return $user;
}

function feature_input(): array {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) throw new InvalidArgumentException('Dados inválidos.');
    return $input;
}

function feature_int(mixed $value, int $min = 1, int $max = PHP_INT_MAX): int {
    $number = filter_var($value, FILTER_VALIDATE_INT);
    if ($number === false || $number < $min || $number > $max) {
        throw new InvalidArgumentException('Valor inteiro fora do intervalo permitido.');
    }
    return $number;
}

function feature_run(callable $action): never {
    try {
        feature_json($action());
    } catch (InvalidArgumentException $e) {
        feature_json(['success' => false, 'message' => $e->getMessage()], 422);
    } catch (DomainException $e) {
        feature_json(['success' => false, 'message' => $e->getMessage()], 409);
    } catch (Throwable $e) {
        error_log('Falha em cupons/maletas/pastas: ' . get_class($e));
        feature_json(['success' => false, 'message' => 'Não foi possível concluir a operação.'], 500);
    }
}
