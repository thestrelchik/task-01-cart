<?php

declare(strict_types=1);

namespace App;

final class Validator
{
    public static function validateName(string $name): ?string
    {
        $name = trim($name);

        if ($name === '') {
            return 'Имя обязательно для заполнения.';
        }

        if (!preg_match('/^\p{L}+(?:\s+\p{L}+)*$/u', $name)) {
            return 'Имя может содержать только буквы (без цифр, запятых и других знаков).';
        }

        return null;
    }

    public static function validateEmail(string $email): ?string
    {
        $email = trim($email);

        if ($email === '') {
            return 'Email обязателен для заполнения.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Укажите корректный email.';
        }

        return null;
    }

    public static function validatePositiveInt(mixed $value, string $fieldLabel): ?string
    {
        if ($value === null || $value === '') {
            return $fieldLabel . ' обязательно.';
        }

        if (!is_numeric($value) || (int) $value != $value || (int) $value < 0) {
            return $fieldLabel . ' должно быть целым неотрицательным числом.';
        }

        return null;
    }
}
