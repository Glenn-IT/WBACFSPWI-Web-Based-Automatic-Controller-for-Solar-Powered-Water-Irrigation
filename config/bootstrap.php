<?php

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
