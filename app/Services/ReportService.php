<?php

declare(strict_types=1);

namespace Transport\Services;

use Transport\Core\Database;

final class ReportService
{
    public function revenueByPeriod(string $period): array
    {
        $pdo = Database::pdo();
        $definition = match ($period) {
            'daily' => [
                'label' => 'DATE(paid_at)',
                'group' => 'DATE(paid_at)',
            ],
            'weekly' => [
                'label' => "CONCAT(YEAR(paid_at), '-W', LPAD(WEEK(paid_at, 1), 2, '0'))",
                'group' => 'YEAR(paid_at), WEEK(paid_at, 1)',
            ],
            'monthly' => [
                'label' => "DATE_FORMAT(paid_at, '%Y-%m')",
                'group' => 'YEAR(paid_at), MONTH(paid_at)',
            ],
            'yearly' => [
                'label' => 'YEAR(paid_at)',
                'group' => 'YEAR(paid_at)',
            ],
            default => [
                'label' => "DATE_FORMAT(paid_at, '%Y-%m')",
                'group' => 'YEAR(paid_at), MONTH(paid_at)',
            ],
        };

        $stmt = $pdo->query("SELECT {$definition['label']} AS period_date, SUM(amount) AS revenue, COUNT(*) AS payments
            FROM payments
            WHERE deleted_at IS NULL AND status = 'successful' AND paid_at IS NOT NULL
            GROUP BY {$definition['group']}
            ORDER BY MIN(paid_at) ASC");
        return $stmt->fetchAll();
    }

    public function summary(): array
    {
        $pdo = Database::pdo();
        return [
            'top_routes' => $pdo->query("SELECT r.origin, r.destination, COUNT(b.id) AS bookings
                FROM routes r
                LEFT JOIN bookings b ON b.route_id = r.id AND b.deleted_at IS NULL
                WHERE r.deleted_at IS NULL
                GROUP BY r.id
                ORDER BY bookings DESC
                LIMIT 10")->fetchAll(),
            'bus_usage' => $pdo->query("SELECT b.bus_number, COUNT(s.id) AS trips
                FROM buses b
                LEFT JOIN schedules s ON s.bus_id = b.id AND s.deleted_at IS NULL
                WHERE b.deleted_at IS NULL
                GROUP BY b.id
                ORDER BY trips DESC
                LIMIT 10")->fetchAll(),
        ];
    }
}
