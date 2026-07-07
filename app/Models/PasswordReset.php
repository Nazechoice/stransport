<?php

declare(strict_types=1);

namespace Transport\Models;

final class PasswordReset extends BaseModel
{
    protected string $table = 'password_resets';

    public function createToken(string $email, string $token, string $expiresAt): bool
    {
        $existing = $this->fetchOne("SELECT id FROM {$this->table} WHERE email = :email LIMIT 1", ['email' => $email]);
        if ($existing) {
            return $this->execute("UPDATE {$this->table} SET token = :token, expires_at = :expires_at, created_at = NOW() WHERE email = :email", [
                'email' => $email,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);
        }

        return $this->execute("INSERT INTO {$this->table} (email, token, expires_at, created_at) VALUES (:email, :token, :expires_at, NOW())", [
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);
    }

    public function findValid(string $email, string $token): ?array
    {
        return $this->fetchOne("SELECT * FROM {$this->table} WHERE email = :email AND token = :token AND expires_at > NOW() LIMIT 1", [
            'email' => $email,
            'token' => $token,
        ]);
    }

    public function deleteForEmail(string $email): bool
    {
        return $this->execute("DELETE FROM {$this->table} WHERE email = :email", ['email' => $email]);
    }
}

