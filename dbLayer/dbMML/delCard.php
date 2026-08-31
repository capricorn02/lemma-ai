<?php
// dbLayer/dbMML/delCard.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$cardId = $_GET['ID'] ?? $_POST['ID'] ?? $_POST['Card_ID'] ?? null;
if (!$cardId) {
    echo json_encode(false);
    exit;
}

$sql = "DELETE FROM cards WHERE Card_ID = :Card_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Card_ID', $cardId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
