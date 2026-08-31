<?php
// dbLayer/dbMML/getFolderList.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$sql = "SELECT Folder_ID, Name, Notes FROM folders ORDER BY Folder_ID DESC";
$stmt = $pdo->prepare($sql);

if ($stmt->execute()) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result ?: [], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
