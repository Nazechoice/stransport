<?php

declare(strict_types=1);

namespace Transport\Models;

use PDO;
use Transport\Core\Database;

abstract class BaseModel
{
    protected PDO $pdo;
    protected string $table;

    public function __construct()
    {
        $this->pdo = Database::pdo();
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function find(int $id): ?array
    {
        return $this->fetchOne("SELECT * FROM {$this->table} WHERE id = :id AND deleted_at IS NULL", ['id' => $id]);
    }

    public function all(): array
    {
        return $this->fetchAll("SELECT * FROM {$this->table} WHERE deleted_at IS NULL ORDER BY id DESC");
    }

    public function delete(int $id): bool
    {
        return $this->execute("UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id", ['id' => $id]);
    }
}
