<?php
// dbLayer/dbMML/setFolder.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

if (isset($_POST['Folder'])) {
    $folder = is_string($_POST['Folder']) ? json_decode($_POST['Folder'], true) : $_POST['Folder'];
    $folderId = $folder['Folder_ID'] ?? null;
    $name = $folder['Name'] ?? $folder['Title'] ?? '';
    $notes = $folder['Notes'] ?? '';
} else {
    $folderId = $_POST['Folder_ID'] ?? $_POST['ID'] ?? null;
    $name = $_POST['Name'] ?? $_POST['Title'] ?? '';
    $notes = $_POST['Notes'] ?? '';
}

if (!$folderId) {
    echo json_encode(false);
    exit;
}

$sql = "UPDATE folders SET Name = :Name, Notes = :Notes WHERE Folder_ID = :Folder_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);
$stmt->bindParam(':Folder_ID', $folderId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
