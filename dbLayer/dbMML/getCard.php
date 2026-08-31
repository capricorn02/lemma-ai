<?php
// dbLayer/dbMML/getCard.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$cardId = $_GET['ID'] ?? $_GET['Card_ID'] ?? null;
if (!$cardId) {
    echo json_encode(false);
    exit;
}

$sql = "SELECT Card_ID, Name, Notes, DemoType_ID FROM cards WHERE Card_ID = :Card_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Card_ID', $cardId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result ?: false, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
