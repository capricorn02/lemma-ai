<?php
// dbLayer/dbMML/getFolderCards.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$folderId = $_GET['ID'] ?? $_GET['Folder_ID'] ?? null;
if (!$folderId) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT c.Card_ID, c.Name, c.Notes, c.DemoType_ID 
        FROM shortcuts s 
        INNER JOIN cards c USING(Card_ID) 
        WHERE s.Folder_ID = :Folder_ID 
        ORDER BY c.Card_ID ASC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Folder_ID', $folderId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result ?: [], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
