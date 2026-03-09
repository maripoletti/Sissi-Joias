<?php
declare(strict_types=1);
require_once __DIR__ . "/../../config/dbh.config.php";

class eventos_model extends Dbh {

    public function getEventos(int $userId): array {
        $pdo = $this->connect();
        $stmt = $pdo->prepare("SELECT EventID AS id, Title AS titulo, Date As date, Hour AS hora, Type AS type, Text AS comentario FROM Events WHERE UserID = ? ORDER BY Date ASC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addEvento(int $userId, string $title, string $date, ?string $hour, string $type, ?string $text): bool {
        $pdo = $this->connect();
        $stmt = $pdo->prepare("INSERT INTO Events (UserID, Title, Date, Hour, Type, Text) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $title, $date, $hour, $type, $text]);
    }

    public function deleteEvento(int $userId, int $eventId): bool {
        $pdo = $this->connect();
        $stmt = $pdo->prepare("DELETE FROM Events WHERE EventID = ? AND UserID = ?");
        return $stmt->execute([$eventId, $userId]);
    }
}