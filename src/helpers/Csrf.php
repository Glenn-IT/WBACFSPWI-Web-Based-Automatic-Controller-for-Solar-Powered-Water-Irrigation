<?php

class Csrf
{
    public static function token(): string
    {
        Auth::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES);
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    public static function verify(?string $token): bool
    {
        Auth::start();
        return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
    }
}
