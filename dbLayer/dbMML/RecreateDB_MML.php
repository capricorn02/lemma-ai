<?php
// dbLayer/dbMML/RecreateDB_MML.php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$defaultSettings = [];
$settingsPath = __DIR__ . '/../../settings_MML.php';
if (file_exists($settingsPath)) {
    $defaultSettings = require $settingsPath;
}

$DB_HOST = $_POST['DB_HOST'] ?? $_GET['DB_HOST'] ?? $_SESSION['MML_DB_HOST'] ?? $defaultSettings['DB_HOST'] ?? 'MySQL-8.2';
$DB_NAME = $_POST['DB_NAME'] ?? $_GET['DB_NAME'] ?? $_SESSION['MML_DB_NAME'] ?? $defaultSettings['DB_NAME'] ?? 'demotest';
$DB_USER = $_POST['DB_USER'] ?? $_GET['DB_USER'] ?? $_SESSION['MML_DB_USER'] ?? $defaultSettings['DB_USER'] ?? 'root';
$DB_PASSWORD = $_POST['DB_PASSWORD'] ?? $_GET['DB_PASSWORD'] ?? $_SESSION['MML_DB_PASSWORD'] ?? $defaultSettings['DB_PASSWORD'] ?? '';

$steps = [];

try {
    // 1. Подключение к СУБД
    $steps[] = ['step' => 'Подключение к MySQL', 'status' => 'info', 'detail' => "Хост: $DB_HOST, Пользователь: $DB_USER"];
    $pdo = new PDO("mysql:host=" . $DB_HOST . ";charset=utf8mb4", $DB_USER, $DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $steps[] = ['step' => 'Авторизация в СУБД', 'status' => 'success', 'detail' => 'Успешное соединение с MySQL'];

    // 2. Создание базы данных
    $pdo->exec("DROP DATABASE IF EXISTS `$DB_NAME`");
    $steps[] = ['step' => 'Очистка старой БД', 'status' => 'success', 'detail' => "DROP DATABASE IF EXISTS `$DB_NAME`"];

    $pdo->exec("CREATE DATABASE `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$DB_NAME`");
    $steps[] = ['step' => 'Создание базы данных', 'status' => 'success', 'detail' => "CREATE DATABASE `$DB_NAME` (utf8mb4_unicode_ci)"];

    // 3. Таблицы
    $tables = [
        'demotypes' => "CREATE TABLE `demotypes` (
            `DemoType_ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `Name` VARCHAR(255) NOT NULL,
            `Notes` MEDIUMTEXT DEFAULT NULL,
            `URL` VARCHAR(255) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'cards' => "CREATE TABLE `cards` (
            `Card_ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `Name` VARCHAR(255) NOT NULL,
            `Notes` MEDIUMTEXT DEFAULT NULL,
            `Demo` LONGBLOB DEFAULT NULL,
            `DemoType_ID` INT UNSIGNED DEFAULT NULL,
            FOREIGN KEY (`DemoType_ID`) REFERENCES `demotypes`(`DemoType_ID`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'folders' => "CREATE TABLE `folders` (
            `Folder_ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `Name` VARCHAR(255) NOT NULL,
            `Notes` MEDIUMTEXT DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'shortcuts' => "CREATE TABLE `shortcuts` (
            `Card_ID` INT UNSIGNED NOT NULL,
            `Folder_ID` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`Card_ID`, `Folder_ID`),
            FOREIGN KEY (`Card_ID`) REFERENCES `cards`(`Card_ID`) ON DELETE CASCADE,
            FOREIGN KEY (`Folder_ID`) REFERENCES `folders`(`Folder_ID`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'scenarios' => "CREATE TABLE `scenarios` (
            `Scenario_ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `Name` VARCHAR(255) NOT NULL,
            `Notes` MEDIUMTEXT DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'slides' => "CREATE TABLE `slides` (
            `Slide_ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `Name` VARCHAR(255) NOT NULL,
            `Notes` MEDIUMTEXT DEFAULT NULL,
            `Scenario_ID` INT UNSIGNED NOT NULL,
            `Order_Num` INT NOT NULL DEFAULT 1,
            `Demo` LONGBLOB DEFAULT NULL,
            `DemoType_ID` INT UNSIGNED DEFAULT NULL,
            FOREIGN KEY (`Scenario_ID`) REFERENCES `scenarios`(`Scenario_ID`) ON DELETE CASCADE,
            FOREIGN KEY (`DemoType_ID`) REFERENCES `demotypes`(`DemoType_ID`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'lectures' => "CREATE TABLE `lectures` (
            `Lecture_ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `Name` VARCHAR(255) NOT NULL,
            `Notes` MEDIUMTEXT DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        'sections' => "CREATE TABLE `sections` (
            `Section_ID` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `Name` VARCHAR(255) NOT NULL,
            `Notes` MEDIUMTEXT DEFAULT NULL,
            `Lecture_ID` INT UNSIGNED NOT NULL,
            `Order_Num` INT NOT NULL DEFAULT 1,
            `Demo` LONGBLOB DEFAULT NULL,
            `DemoType_ID` INT UNSIGNED DEFAULT NULL,
            `Video` LONGBLOB DEFAULT NULL,
            `Commands` MEDIUMTEXT DEFAULT NULL,
            FOREIGN KEY (`Lecture_ID`) REFERENCES `lectures`(`Lecture_ID`) ON DELETE CASCADE,
            FOREIGN KEY (`DemoType_ID`) REFERENCES `demotypes`(`DemoType_ID`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    ];

    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        $steps[] = ['step' => "Таблица `$name`", 'status' => 'success', 'detail' => 'Структура успешно создана'];
    }

    // 4. Инициализация базовых демо-типов
    $pdo->exec("INSERT INTO `demotypes` (`DemoType_ID`, `Name`, `Notes`, `URL`) VALUES
        (1, '2D Растр', 'Статические 2D изображения с поддержкой зума, панорамы и пера', '/DemoObjectTest/js/slideRastr2d.js'),
        (2, 'Видео', 'Видеодемонстрация с управлением воспроизведением и маркером', '/DemoObjectTest/js/slideVideo.js'),
        (3, 'Текст', 'Текстовые слайды и форматированные заметки', NULL),
        (4, '3D Модель', 'Интерактивные 3D объекты', NULL)
    ");
    $steps[] = ['step' => 'Инициализация demotypes', 'status' => 'success', 'detail' => 'Добавлено 4 базовых типа (2D Растр, Видео, Текст, 3D Модель)'];

    // 5. Сохраняем в сессии
    $_SESSION['MML_DB_HOST'] = $DB_HOST;
    $_SESSION['MML_DB_NAME'] = $DB_NAME;
    $_SESSION['MML_DB_USER'] = $DB_USER;
    $_SESSION['MML_DB_PASSWORD'] = $DB_PASSWORD;

    echo json_encode([
        'status' => true,
        'message' => "База данных `$DB_NAME` успешно создана со всеми 8 таблицами MML!",
        'dbname' => $DB_NAME,
        'host' => $DB_HOST,
        'steps' => $steps
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    $steps[] = ['step' => 'Ошибка выполнения', 'status' => 'error', 'detail' => $e->getMessage()];
    echo json_encode([
        'status' => false,
        'error' => $e->getMessage(),
        'steps' => $steps
    ], JSON_UNESCAPED_UNICODE);
}
?>
