<?php
// dbLayer/dbMML/delFolder.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$folderId = $_GET['ID'] ?? $_POST['ID'] ?? $_POST['Folder_ID'] ?? null;
if (!$folderId) {
    echo json_encode(false);
    exit;
}

$sql = "DELETE FROM folders WHERE Folder_ID = :Folder_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Folder_ID', $folderId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
