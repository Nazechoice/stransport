<?php

declare(strict_types=1);

namespace Transport\Models;

final class Route extends BaseModel
{
    protected string $table = 'routes';

    public function searchable(): array
    {
        return $this->fetchAll("SELECT * FROM {$this->table} WHERE deleted_at IS NULL AND status = 'active' ORDER BY origin, destination");
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (origin, destination, stops, distance_km, estimated_minutes, fare, status, created_at, updated_at)
                VALUES (:origin, :destination, :stops, :distance_km, :estimated_minutes, :fare, :status, NOW(), NOW())";
        $this->execute($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }
}

