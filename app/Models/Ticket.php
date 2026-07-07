<?php

declare(strict_types=1);

namespace Transport\Models;

final class Ticket extends BaseModel
{
    protected string $table = 'tickets';

    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (ticket_number, booking_id, qr_token, qr_data, status, issued_by, issued_at, created_at, updated_at)
                VALUES (:ticket_number, :booking_id, :qr_token, :qr_data, :status, :issued_by, :issued_at, NOW(), NOW())";
        $this->execute($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }
}

