<?php

declare(strict_types=1);

namespace Transport\Models;

final class Bus extends BaseModel
{
    protected string $table = 'buses';

    public function available(): array
    {
        return $this->fetchAll("SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND status IN ('active','maintenance') ORDER BY bus_number");
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (bus_number, registration_number, bus_type, capacity, status, maintenance_notes, driver_id, image, created_at, updated_at)
                VALUES (:bus_number, :registration_number, :bus_type, :capacity, :status, :maintenance_notes, :driver_id, :image, NOW(), NOW())";
        $this->execute($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }
}

