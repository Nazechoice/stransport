<?php

declare(strict_types=1);

namespace Transport\Models;

final class User extends BaseModel
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne("SELECT * FROM {$this->table} WHERE email = :email AND deleted_at IS NULL LIMIT 1", ['email' => $email]);
    }

    public function findByRole(string $role): array
    {
        return $this->fetchAll("SELECT * FROM {$this->table} WHERE role = :role AND deleted_at IS NULL ORDER BY id DESC", ['role' => $role]);
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (full_name, email, phone, password_hash, role, status, photo, remember_token, created_at, updated_at)
                VALUES (:full_name, :email, :phone, :password_hash, :role, :status, :photo, :remember_token, NOW(), NOW())";
        $this->execute($sql, $data);
        return (int) $this->pdo->lastInsertId();
    }

    public function updateProfile(int $id, array $data): bool
    {
        $sql = "UPDATE {$this->table} SET full_name = :full_name, phone = :phone, photo = :photo, updated_at = NOW() WHERE id = :id";
        $data['id'] = $id;
        return $this->execute($sql, $data);
    }

    public function updatePassword(int $id, string $hash): bool
    {
        return $this->execute("UPDATE {$this->table} SET password_hash = :password_hash, updated_at = NOW() WHERE id = :id", [
            'id' => $id,
            'password_hash' => $hash,
        ]);
    }

    public function setRememberToken(int $id, ?string $token): bool
    {
        return $this->execute("UPDATE {$this->table} SET remember_token = :remember_token, updated_at = NOW() WHERE id = :id", [
            'id' => $id,
            'remember_token' => $token,
        ]);
    }
}

