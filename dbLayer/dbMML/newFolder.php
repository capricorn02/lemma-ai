<?php
// dbLayer/dbMML/newFolder.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$name = $_POST['Name'] ?? $_POST['Title'] ?? 'Новая папка';
$notes = $_POST['Notes'] ?? '';

$sql = "INSERT INTO folders (Name, Notes) VALUES (:Name, :Notes)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);

if ($stmt->execute()) {
    $id = (int)$pdo->lastInsertId();
    $folder = [
        'Folder_ID' => $id,
        'Name' => $name,
        'Notes' => $notes
    ];
    echo json_encode($folder, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
