<?php
declare(strict_types=1);

class RankService {
    // Metas em centavos, na mesma ordem dos níveis do cadastro.
    public const METAS = ['ametista' => 0, 'safira' => 80000, 'topazio' => 130000,
        'esmeralda' => 180000, 'rubi' => 230000, 'rubi-black' => 500000];
    private const NOMES = ['ametista' => 'Ametista', 'safira' => 'Safira', 'topazio' => 'Topázio',
        'esmeralda' => 'Esmeralda', 'rubi' => 'Rubi', 'rubi-black' => 'Rubi Black'];

    public static function centavos(string $total): int {
        if (!preg_match('/^(-?\d+)(?:\.(\d{1,2}))?$/D', $total, $parts)) {
            throw new InvalidArgumentException('Total de vendas inválido.');
        }
        $cents = abs((int)$parts[1]) * 100 + (int)str_pad($parts[2] ?? '', 2, '0');
        return str_starts_with($total, '-') ? -$cents : $cents;
    }

    public static function nivelPorTotal(int $total): string {
        $level = 'ametista';
        foreach (self::METAS as $name => $goal) {
            if ($total >= $goal) $level = $name;
        }
        return $level;
    }

    public static function promocao(string $current, int $before, int $after): ?string {
        $levels = array_keys(self::METAS);
        $candidate = self::nivelPorTotal($after);
        if (self::nivelPorTotal($before) === $candidate || $after <= $before) return null;
        return array_search($candidate, $levels, true) > array_search($current, $levels, true) ? $candidate : null;
    }

    public static function perfil(string $level, int $total): array {
        if (!isset(self::METAS[$level])) $level = 'ametista';
        $levels = array_keys(self::METAS);
        // Uma alteração manual para baixo não reaplica metas já ultrapassadas.
        $nextIndex = max(array_search($level, $levels, true), array_search(self::nivelPorTotal($total), $levels, true)) + 1;
        $next = $levels[$nextIndex] ?? null;
        return ['nivel' => $level, 'nivel_nome' => self::NOMES[$level],
            'proximo_nivel' => $next === null ? null : self::NOMES[$next],
            'proxima_meta' => $next === null ? null : self::METAS[$next] / 100];
    }

    private static function total(PDO $pdo, int $user): int {
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(so.Sales), 0) FROM Sales_Orders so
            JOIN Sales_Customers c ON c.CustomerID = so.CustomerID
            JOIN Sales_Employees e ON e.EmployeeID = c.EmployeeID
            WHERE e.UserID = ? AND so.Status = 1');
        $stmt->execute([$user]);
        return self::centavos((string)$stmt->fetchColumn());
    }

    public static function iniciarVenda(PDO $pdo, int $user): ?array {
        // Serializa vendas da mesma revendedora e alterações manuais do seu nível.
        $stmt = $pdo->prepare('SELECT pending.PendUserID, pending.Level FROM Temp_PendingUsers pending
            JOIN Auth_Users auth ON auth.Email = pending.Email
            WHERE auth.UserID = ? AND pending.Status = "aprovado" FOR UPDATE');
        $stmt->execute([$user]);
        $pending = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pending) return null;
        return ['id' => (int)$pending['PendUserID'], 'nivel' => $pending['Level'], 'total' => self::total($pdo, $user)];
    }

    public static function finalizarVenda(PDO $pdo, int $user, ?array $previous): void {
        if ($previous === null) return;
        $next = self::promocao($previous['nivel'], $previous['total'], self::total($pdo, $user));
        if ($next === null) return;
        $stmt = $pdo->prepare('UPDATE Temp_PendingUsers SET Level = ? WHERE PendUserID = ? AND Level = ?');
        $stmt->execute([$next, $previous['id'], $previous['nivel']]);
    }
}
