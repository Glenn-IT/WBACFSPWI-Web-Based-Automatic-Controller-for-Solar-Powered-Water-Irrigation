<?php

require_once __DIR__ . '/../models/User.php';

class AuthService
{
    // Returns the user array on success, or null on invalid credentials / inactive account.
    public static function attempt(string $email, string $password): ?array
    {
        $user = User::findByEmail($email);

        if (!$user || !$user['is_active']) {
            return null;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return null;
        }

        User::updateLastLogin((int) $user['id']);

        return $user;
    }
}
