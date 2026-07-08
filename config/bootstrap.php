<?php

// Web path to the public/ directory, so URLs work whether the app is served
// from a subfolder (e.g. XAMPP htdocs) or as the web root ('' in that case).
$publicDir = str_replace('\\', '/', (string) realpath(__DIR__ . '/../public'));
$docRoot = str_replace('\\', '/', (string) realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
define('BASE_URL', ($docRoot !== '' && stripos($publicDir, $docRoot) === 0)
    ? rtrim(substr($publicDir, strlen($docRoot)), '/')
    : '');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/device.php';
require_once __DIR__ . '/../src/helpers/Auth.php';
require_once __DIR__ . '/../src/helpers/Csrf.php';
require_once __DIR__ . '/../src/helpers/DeviceAuth.php';
require_once __DIR__ . '/../src/helpers/RateLimiter.php';

spl_autoload_register(function (string $class) {
    $dirs = [
        __DIR__ . '/../src/models/',
        __DIR__ . '/../src/services/',
        __DIR__ . '/../src/controllers/',
    ];

    foreach ($dirs as $dir) {
        $path = $dir . $class . '.php';
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

Auth::start();
