<?php
// dbLayer/dbMML/reconnectDB_MML.php
// Восстанавливаем соединение с сервером БД MML из сессии или настроек

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$defaultSettings = [];
$settingsPath = __DIR__ . '/../../settings_MML.php';
if (file_exists($settingsPath)) {
    $defaultSettings = require $settingsPath;
}

$DB_HOST = $_SESSION['MML_DB_HOST'] ?? $defaultSettings['DB_HOST'] ?? 'MySQL-8.2';
$DB_NAME = $_SESSION['MML_DB_NAME'] ?? $defaultSettings['DB_NAME'] ?? 'demotest';
$DB_USER = $_SESSION['MML_DB_USER'] ?? $defaultSettings['DB_USER'] ?? 'root';
$DB_PASSWORD = $_SESSION['MML_DB_PASSWORD'] ?? $defaultSettings['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host=" . $DB_HOST . ";dbname=" . $DB_NAME . ";charset=utf8mb4", $DB_USER, $DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}
?>
