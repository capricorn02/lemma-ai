<?php
// dbLayer/dbMML/delShortcut.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$cardId = $_POST['Card_ID'] ?? $_POST['CardID'] ?? $_GET['Card_ID'] ?? null;
$folderId = $_POST['Folder_ID'] ?? $_POST['FolderID'] ?? $_GET['Folder_ID'] ?? null;

if (!$cardId || !$folderId) {
    echo json_encode(false);
    exit;
}

$sql = "DELETE FROM shortcuts WHERE Card_ID = :Card_ID AND Folder_ID = :Folder_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Card_ID', $cardId, PDO::PARAM_INT);
$stmt->bindParam(':Folder_ID', $folderId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
