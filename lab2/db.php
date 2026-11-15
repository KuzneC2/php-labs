<?php

if (!defined('LAB2_ENTRY')) {
    http_response_code(403);
    exit('Доступ запрещён');
}

$config = require __DIR__ . '/../config.local.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
        $config['username'],
        $config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

} catch (PDOException $e) {
    http_response_code(500);
    exit('Ошибка подключения к базе данных.');
}
