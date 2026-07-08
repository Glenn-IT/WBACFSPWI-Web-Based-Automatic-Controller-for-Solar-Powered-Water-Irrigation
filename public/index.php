<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (Auth::check()) {
    header('Location: ' . BASE_URL . '/admin/dashboard.php');
} else {
    header('Location: ' . BASE_URL . '/login.php');
}
exit;
