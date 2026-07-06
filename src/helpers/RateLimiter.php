<?php

// File-based sliding-window rate limiter (no external cache/queue in this stack).
class RateLimiter
{
    private static function storageDir(): string
    {
        $dir = __DIR__ . '/../../storage/ratelimit';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    // Halts the request with 429 JSON if $key has exceeded $maxRequests within $windowSeconds.
    public static function enforce(string $key, int $maxRequests, int $windowSeconds): void
    {
        $safeKey = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $key);
        $file = self::storageDir() . '/' . $safeKey . '.json';

        $fp = fopen($file, 'c+');
        flock($fp, LOCK_EX);

        $raw = stream_get_contents($fp);
        $timestamps = $raw ? json_decode($raw, true) : [];
        if (!is_array($timestamps)) {
            $timestamps = [];
        }

        $now = time();
        $timestamps = array_values(array_filter($timestamps, fn($t) => $t > $now - $windowSeconds));

        if (count($timestamps) >= $maxRequests) {
            flock($fp, LOCK_UN);
            fclose($fp);
            http_response_code(429);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Too many requests, slow down.']);
            exit;
        }

        $timestamps[] = $now;
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($timestamps));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}
