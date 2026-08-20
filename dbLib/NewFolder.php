<?php
require_once 'reconnectDB.php'; 
$name = $_POST['Name'] ?? 'Новая презентация';
$SQL = "INSERT INTO folders (title, description) VALUES (:Name, 'Создано при импорте папки')";
$stmt = $pdo->prepare($SQL);
$stmt->bindParam(':Name', $name);
if ($stmt->execute()) {
    $ID = $pdo->lastInsertId();
    $folder = new class{};
    $folder->Folder_ID = $ID;
    $folder->Title = $name;
    echo json_encode($folder, JSON_UNESCAPED_UNICODE);
} else {
    echo false;
}