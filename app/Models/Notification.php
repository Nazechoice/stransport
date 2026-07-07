<?php

declare(strict_types=1);

namespace Transport\Models;

final class Notification extends BaseModel
{
    protected string $table = 'notifications';

    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (user_id, title, message, type, is_read, created_at, updated_at)
                VALUES (:user_id, :title, :message, :type, :is_read, NOW(), NOW())";
        $this->execute($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }

    public function forUser(int $userId): array
    {
        return $this->fetchAll("SELECT * FROM {$this->table} WHERE user_id = :user_id AND deleted_at IS NULL ORDER BY id DESC", [
            'user_id' => $userId,
        ]);
    }
}
