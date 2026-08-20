<?php
session_start();
$config = require '../settings.php';
$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], $options);
    $_SESSION['db_connection_params'] = [
        'dsn'      => $dsn,
        'username' => $config['username'],
        'password' => $config['password'],
        'options'  => $options
    ];
    echo json_encode(['status' => true]);
} catch (PDOException $e) {
    echo json_encode(['status' => false, 'error' => $e->getMessage()]);
}