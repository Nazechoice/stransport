<?php

declare(strict_types=1);

namespace Transport\Core;

final class Validator
{
    public static function required(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $label) {
            $value = trim((string)($data[$field] ?? ''));
            if ($value === '') {
                $errors[$field] = "{$label} is required.";
            }
        }

        return $errors;
    }

    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function int(string|int|null $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}

