<?php

namespace RB\System\Helper;

class PasswordHelper
{
    public static function getHash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 8]);
    }

    public static function isVerify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function isValid(string $password): bool
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password) > 0;
    }
}