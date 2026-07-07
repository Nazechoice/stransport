<?php

declare(strict_types=1);

namespace Transport\Models;

final class Payment extends BaseModel
{
    protected string $table = 'payments';

    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (booking_id, amount, method, payment_reference, status, paid_at, received_by, created_at, updated_at)
                VALUES (:booking_id, :amount, :method, :payment_reference, :status, :paid_at, :received_by, NOW(), NOW())";
        $this->execute($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }
}

