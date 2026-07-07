<?php

declare(strict_types=1);

namespace Transport\Controllers;

use Transport\Core\Database;
use Transport\Services\QrCodeService;
use Transport\Services\SeatLayoutService;

final class ApiController extends BaseController
{
    public function seats(int $scheduleId): void
    {
        $scheduleStmt = Database::pdo()->prepare("SELECT s.*, b.capacity AS bus_capacity
            FROM schedules s
            INNER JOIN buses b ON b.id = s.bus_id
            WHERE s.id = :id AND s.deleted_at IS NULL LIMIT 1");
        $scheduleStmt->execute(['id' => $scheduleId]);
        $schedule = $scheduleStmt->fetch() ?: [];
        $seats = (new SeatLayoutService())->matrixForSchedule($scheduleId, (int) ($schedule['bus_capacity'] ?? 0));

        $this->json(['schedule_id' => $scheduleId, 'seats' => $seats]);
    }

    public function qr(string $payload): void
    {
        header('Content-Type: image/svg+xml; charset=utf-8');
        echo (new QrCodeService())->renderSvg($payload);
        exit;
    }
}
