<?php
// dbLib/RecreateDB.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Читаем настройки напрямую с диска
$config = require '../settings.php';

try {
    // Подключаемся к MySQL без указания конкретной БД (так как мы собираемся её удалить)
    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $dbName = $config['dbname'];

    // 1. Полностью удаляем старую базу данных
    $pdo->exec("DROP DATABASE IF EXISTS `$dbName`");

    // 2. Создаем чистую базу данных
    $pdo->exec("CREATE DATABASE `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbName`");

    // 3. Таблица Карточек
    $pdo->exec("CREATE TABLE `cards` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `body` LONGBLOB DEFAULT NULL,
        `mime_type` VARCHAR(100) DEFAULT 'application/octet-stream',
        `notes` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // 4. Таблица Фолдеров
    $pdo->exec("CREATE TABLE `folders` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // 5. Ассоциативная таблица Ярлыков (M:M)
    $pdo->exec("CREATE TABLE `card_folder_labels` (
        `card_id` INT UNSIGNED NOT NULL,
        `folder_id` INT UNSIGNED NOT NULL,
        PRIMARY KEY (`card_id`, `folder_id`),
        FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE CASCADE,
        FOREIGN KEY (`folder_id`) REFERENCES `folders` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB");

    echo json_encode(['status' => true, 'message' => 'База данных успешно пересоздана с нуля!']);

} catch (PDOException $e) {
    echo json_encode(['status' => false, 'error' => $e->getMessage()]);
}