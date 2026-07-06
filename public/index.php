<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (Auth::check()) {
    header('Location: /admin/dashboard.php');
} else {
    header('Location: /login.php');
}
exit;
