<?php

declare(strict_types=1);

namespace Transport\Models;

final class ScheduleSeat extends BaseModel
{
    protected string $table = 'schedule_seats';

    public function forSchedule(int $scheduleId): array
    {
        return $this->fetchAll("SELECT * FROM {$this->table} WHERE schedule_id = :schedule_id AND deleted_at IS NULL ORDER BY seat_number", [
            'schedule_id' => $scheduleId,
        ]);
    }
}

