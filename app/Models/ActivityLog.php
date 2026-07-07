<?php

declare(strict_types=1);

namespace Transport\Models;

final class ActivityLog extends BaseModel
{
    protected string $table = 'activity_logs';

    public function record(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (user_id, action, module, description, ip_address, user_agent, created_at, updated_at)
                VALUES (:user_id, :action, :module, :description, :ip_address, :user_agent, NOW(), NOW())";
        $this->execute($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }

    public function recent(int $limit = 50): array
    {
        return $this->fetchAll("SELECT a.*, u.full_name FROM {$this->table} a LEFT JOIN users u ON u.id = a.user_id ORDER BY a.id DESC LIMIT {$limit}");
    }
}

