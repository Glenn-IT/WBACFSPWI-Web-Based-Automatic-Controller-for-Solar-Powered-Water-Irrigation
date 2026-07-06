<?php

class DeviceAuth
{
    // Halts the request with 401 JSON if the X-API-Key header doesn't match DEVICE_API_KEY.
    public static function requireValidKey(): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        RateLimiter::enforce("device:$ip", 60, 60);

        $headers = function_exists('getallheaders') ? getallheaders() : [];
        $key = $headers['X-Api-Key'] ?? $headers['X-API-Key'] ?? ($_SERVER['HTTP_X_API_KEY'] ?? '');

        if (!hash_equals(DEVICE_API_KEY, (string) $key)) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }
}
