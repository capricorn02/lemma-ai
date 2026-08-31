<?php
// dbLayer/dbMML/getFolder.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$folderId = $_GET['ID'] ?? $_GET['Folder_ID'] ?? null;
if (!$folderId) {
    echo json_encode(false);
    exit;
}

$sql = "SELECT Folder_ID, Name, Notes FROM folders WHERE Folder_ID = :Folder_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Folder_ID', $folderId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result ?: false, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
