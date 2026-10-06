<?php
// Set default Content-Type header agar browser merender sebagai web (bukan download)
header('Content-Type: text/html; charset=utf-8');

// Set working directory ke root project
chdir(dirname(__DIR__));

// Normalisasi SCRIPT_NAME dan SCRIPT_FILENAME untuk routing CodeIgniter
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'index.php';

// Deteksi HTTPS dari edge proxy Vercel
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Eksekusi CodeIgniter front controller
require __DIR__ . '/../index.php';
