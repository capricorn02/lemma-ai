<?php
require_once 'reconnectDB.php'; 
$cardId = (int)$_POST['Card_ID'];
$folderId = (int)$_POST['Folder_ID'];
$SQL = "INSERT INTO card_folder_labels (card_id, folder_id) VALUES (:CardID, :FolderID)";
$stmt = $pdo->prepare($SQL);
$stmt->bindParam(':CardID', $cardId);
$stmt->bindParam(':FolderID', $folderId);
if ($stmt->execute()) {
    echo json_encode(['status' => true]);
} else {
    echo false;
}