<?php

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool
    {
        self::start();
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        self::start();
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'role' => $_SESSION['user_role'],
        ];
    }

    public static function login(array $user): void
    {
        self::start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
    }

    public static function logout(): void
    {
        self::start();
        $_SESSION = [];
        session_destroy();
    }

    // Redirects to login if not authenticated. Call at the top of any protected page.
    public static function requireLogin(?string $loginUrl = null): void
    {
        if (!self::check()) {
            header('Location: ' . ($loginUrl ?? BASE_URL . '/login.php'));
            exit;
        }
    }

    // Redirects away if the current user's role isn't in $roles.
    public static function requireRole(array $roles, ?string $redirectUrl = null): void
    {
        self::requireLogin();
        $user = self::user();
        if (!in_array($user['role'], $roles, true)) {
            header('Location: ' . ($redirectUrl ?? BASE_URL . '/admin/dashboard.php'));
            exit;
        }
    }
}
