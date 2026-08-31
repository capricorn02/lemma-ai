<?php
// dbLayer/dbMML/connectDB_MML.php
// Устанавливаем соединение с СУБД MySQL для MML и проверяем доступность БД

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$defaultSettings = [];
$settingsPath = __DIR__ . '/../../settings_MML.php';
if (file_exists($settingsPath)) {
    $defaultSettings = require $settingsPath;
}

$DB_HOST = $_POST['DB_HOST'] ?? $_GET['DB_HOST'] ?? $defaultSettings['DB_HOST'] ?? 'MySQL-8.2';
$DB_NAME = $_POST['DB_NAME'] ?? $_GET['DB_NAME'] ?? $defaultSettings['DB_NAME'] ?? 'demotest';
$DB_USER = $_POST['DB_USER'] ?? $_GET['DB_USER'] ?? $defaultSettings['DB_USER'] ?? 'root';
$DB_PASSWORD = $_POST['DB_PASSWORD'] ?? $_GET['DB_PASSWORD'] ?? $defaultSettings['DB_PASSWORD'] ?? '';

try {
    // 1. Подключаемся к СУБД MySQL без привязки к конкретной БД
    $pdo = new PDO("mysql:host=" . $DB_HOST . ";charset=utf8mb4", $DB_USER, $DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);

    // 2. Проверяем, существует ли указанная БД
    $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :dbname");
    $stmt->bindParam(':dbname', $DB_NAME);
    $stmt->execute();
    $dbExists = (bool)$stmt->fetchColumn();

    if ($dbExists) {
        $pdo->exec("USE `$DB_NAME`");
        $msg = "Соединение с СУБД успешно. База данных `$DB_NAME` найдена и готова к работе.";
    } else {
        $msg = "Соединение с СУБД успешно. База данных `$DB_NAME` пока не создана (будет создана при развертывании).";
    }

    $_SESSION['MML_DB_HOST'] = $DB_HOST;
    $_SESSION['MML_DB_NAME'] = $DB_NAME;
    $_SESSION['MML_DB_USER'] = $DB_USER;
    $_SESSION['MML_DB_PASSWORD'] = $DB_PASSWORD;

    echo json_encode([
        'status' => true,
        'db_exists' => $dbExists,
        'message' => $msg,
        'host' => $DB_HOST,
        'dbname' => $DB_NAME
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'db_exists' => false,
        'error' => 'Ошибка подключения к MySQL: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>
