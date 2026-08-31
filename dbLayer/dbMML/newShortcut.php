<?php
// dbLayer/dbMML/newShortcut.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$cardId = $_POST['Card_ID'] ?? $_POST['CardID'] ?? null;
$folderId = $_POST['Folder_ID'] ?? $_POST['FolderID'] ?? null;

if (!$cardId || !$folderId) {
    echo json_encode(false);
    exit;
}

$sql = "INSERT IGNORE INTO shortcuts (Card_ID, Folder_ID) VALUES (:Card_ID, :Folder_ID)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Card_ID', $cardId, PDO::PARAM_INT);
$stmt->bindParam(':Folder_ID', $folderId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
