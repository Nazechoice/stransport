<?php

declare(strict_types=1);

namespace Transport\Models;

final class SeatBlock extends BaseModel
{
    protected string $table = 'seat_blocks';

    public function blockedForSchedule(int $scheduleId): array
    {
        return $this->fetchAll("SELECT seat_number FROM {$this->table} WHERE schedule_id = :schedule_id AND deleted_at IS NULL", [
            'schedule_id' => $scheduleId,
        ]);
    }
}

