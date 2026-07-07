<?php

declare(strict_types=1);

namespace Transport\Services;

use Transport\Core\Database;

final class StatsService
{
    public function counts(): array
    {
        $pdo = Database::pdo();
        return [
            'users' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL")->fetchColumn(),
            'drivers' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND role = 'driver'")->fetchColumn(),
            'passengers' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND role = 'passenger'")->fetchColumn(),
            'buses' => (int) $pdo->query("SELECT COUNT(*) FROM buses WHERE deleted_at IS NULL")->fetchColumn(),
            'routes' => (int) $pdo->query("SELECT COUNT(*) FROM routes WHERE deleted_at IS NULL")->fetchColumn(),
            'schedules' => (int) $pdo->query("SELECT COUNT(*) FROM schedules WHERE deleted_at IS NULL")->fetchColumn(),
            'bookings' => (int) $pdo->query("SELECT COUNT(*) FROM bookings WHERE deleted_at IS NULL")->fetchColumn(),
            'tickets' => (int) $pdo->query("SELECT COUNT(*) FROM tickets WHERE deleted_at IS NULL")->fetchColumn(),
            'revenue' => (float) $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE deleted_at IS NULL AND status = 'successful'")->fetchColumn(),
            'pending_payments' => (int) $pdo->query("SELECT COUNT(*) FROM payments WHERE deleted_at IS NULL AND status = 'pending'")->fetchColumn(),
        ];
    }

    public function recentBookings(int $limit = 5): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT b.*, u.full_name AS passenger_name, r.origin, r.destination, s.departure_date, s.departure_time
            FROM bookings b
            INNER JOIN users u ON u.id = b.passenger_id
            INNER JOIN schedules s ON s.id = b.schedule_id
            INNER JOIN routes r ON r.id = s.route_id
            WHERE b.deleted_at IS NULL
            ORDER BY b.id DESC
            LIMIT {$limit}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function recentPayments(int $limit = 5): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT p.*, b.booking_number, u.full_name AS passenger_name
            FROM payments p
            INNER JOIN bookings b ON b.id = p.booking_id
            INNER JOIN users u ON u.id = b.passenger_id
            WHERE p.deleted_at IS NULL
            ORDER BY p.id DESC
            LIMIT {$limit}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function popularRoutes(int $limit = 8): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare("SELECT r.origin, r.destination, COUNT(b.id) AS bookings
            FROM routes r
            LEFT JOIN bookings b ON b.route_id = r.id AND b.deleted_at IS NULL
            WHERE r.deleted_at IS NULL
            GROUP BY r.id
            ORDER BY bookings DESC, r.origin ASC
            LIMIT {$limit}");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
