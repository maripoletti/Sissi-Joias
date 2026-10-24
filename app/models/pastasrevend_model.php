<?php
declare(strict_types=1);
require_once __DIR__ . '/../../config/dbh.config.php';

class Pastasrevend_model extends Dbh {
    public function pastas(): array {
        return $this->connect()->query('SELECT e.UserID AS id, e.FullName AS nome, COUNT(f.FileID) AS quantidade
            FROM Sales_Employees e LEFT JOIN Sales_EmployeeFiles f ON f.UserID = e.UserID
            GROUP BY e.UserID, e.FullName ORDER BY e.FullName')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar(int $user): array {
        $stmt = $this->connect()->prepare('SELECT FileID AS id, UserID AS revendedora_id, FileName AS nome,
            FileSize AS tamanho, CreatedAt AS criado_em, Checked AS assinalado
            FROM Sales_EmployeeFiles WHERE UserID = ? ORDER BY FileID DESC');
        $stmt->execute([$user]);
        return array_map(function ($file) {
            $file['url'] = '/api/arquivos/baixar?id=' . (int)$file['id'];
            return $file;
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public static function salvar(PDO $pdo, int $user, string $name, string $mime, string $content, ?int $case = null): int {
        $stmt = $pdo->prepare('INSERT INTO Sales_EmployeeFiles (UserID, FileName, MimeType, FileSize, Content, CaseID)
            VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bindValue(1, $user, PDO::PARAM_INT);
        $stmt->bindValue(2, $name);
        $stmt->bindValue(3, $mime);
        $stmt->bindValue(4, strlen($content), PDO::PARAM_INT);
        $stmt->bindValue(5, $content, PDO::PARAM_LOB);
        $stmt->bindValue(6, $case, $case === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->execute();
        return (int)$pdo->lastInsertId();
    }

    public function upload(int $user, array $file): int {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new InvalidArgumentException('Falha no upload. Confira o arquivo e o limite do servidor.');
        }
        if (!is_uploaded_file($file['tmp_name']) || filesize($file['tmp_name']) > 10 * 1024 * 1024 || filesize($file['tmp_name']) < 1) {
            throw new InvalidArgumentException('Envie um arquivo de até 10 MB.');
        }
        $name = basename(str_replace('\\', '/', $file['name']));
        $name = mb_substr(preg_replace('/[\x00-\x1F\x7F]/u', '', $name), 0, 180);
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $types = ['pdf' => 'application/pdf', 'png' => 'image/png', 'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg', 'txt' => 'text/plain'];
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!isset($types[$extension]) || $types[$extension] !== $mime) {
            throw new InvalidArgumentException('Formatos permitidos: PDF, PNG, JPG e TXT.');
        }
        $pdo = $this->connect();
        $stmt = $pdo->prepare('SELECT UserID FROM Sales_Employees WHERE UserID = ?');
        $stmt->execute([$user]);
        if (!$stmt->fetchColumn()) throw new InvalidArgumentException('Revendedora não encontrada.');
        return self::salvar($pdo, $user, $name, $mime, file_get_contents($file['tmp_name']));
    }

    public function baixar(int $id, ?int $user): array {
        $stmt = $this->connect()->prepare('SELECT FileName, MimeType, Content FROM Sales_EmployeeFiles
            WHERE FileID = ?' . ($user === null ? '' : ' AND UserID = ?'));
        $stmt->execute($user === null ? [$id] : [$id, $user]);
        $file = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$file) throw new InvalidArgumentException('Arquivo não encontrado.');
        return $file;
    }

    public function excluir(int $id, int $user): void {
        $stmt = $this->connect()->prepare('DELETE FROM Sales_EmployeeFiles WHERE FileID = ? AND UserID = ?');
        $stmt->execute([$id, $user]);
    }

    public function assinalar(int $id, int $user, bool $checked): void {
        $stmt = $this->connect()->prepare('UPDATE Sales_EmployeeFiles SET Checked = ? WHERE FileID = ? AND UserID = ?');
        $stmt->execute([(int)$checked, $id, $user]);
    }
}
