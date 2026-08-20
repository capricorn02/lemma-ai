<?php
require_once 'reconnectDB.php'; 
$id = (int)$_POST['Card_ID'];
$fileTmp = $_FILES['BlobFile']['tmp_name'];
$mimeType = $_FILES['BlobFile']['type'];
$fileData = file_get_contents($fileTmp);

$SQL = "UPDATE cards SET body = :Body, mime_type = :Mime WHERE id = :ID";
$stmt = $pdo->prepare($SQL);
$stmt->bindParam(':Body', $fileData, PDO::PARAM_LOB);
$stmt->bindParam(':Mime', $mimeType);
$stmt->bindParam(':ID', $id);
if ($stmt->execute()) {
    echo json_encode(['status' => true]);
} else {
    echo false;
}