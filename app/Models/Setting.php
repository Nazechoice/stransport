<?php

declare(strict_types=1);

namespace Transport\Models;

final class Setting extends BaseModel
{
    protected string $table = 'system_settings';

    public function allKeyValue(): array
    {
        $rows = $this->fetchAll("SELECT setting_key, setting_value FROM {$this->table} WHERE deleted_at IS NULL");
        $map = [];
        foreach ($rows as $row) {
            $map[$row['setting_key']] = $row['setting_value'];
        }
        return $map;
    }

    public function upsert(string $key, string $value, ?string $group = null): bool
    {
        $existing = $this->fetchOne("SELECT id FROM {$this->table} WHERE setting_key = :setting_key AND deleted_at IS NULL LIMIT 1", [
            'setting_key' => $key,
        ]);

        if ($existing) {
            return $this->execute("UPDATE {$this->table} SET setting_value = :setting_value, setting_group = :setting_group, updated_at = NOW() WHERE setting_key = :setting_key", [
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => $group,
            ]);
        }

        return $this->execute("INSERT INTO {$this->table} (setting_key, setting_value, setting_group, created_at, updated_at) VALUES (:setting_key, :setting_value, :setting_group, NOW(), NOW())", [
            'setting_key' => $key,
            'setting_value' => $value,
            'setting_group' => $group,
        ]);
    }
}

