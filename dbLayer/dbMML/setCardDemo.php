<?php
// dbLayer/dbMML/setCardDemo.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$cardId = $_POST['ID'] ?? $_POST['Card_ID'] ?? null;
$demoFile = $_FILES['Demo'] ?? $_FILES['Body'] ?? null;

if (!$cardId || !$demoFile || !is_uploaded_file($demoFile['tmp_name'])) {
    echo json_encode(false);
    exit;
}

$demoData = file_get_contents($demoFile['tmp_name']);

$sql = "UPDATE cards SET Demo = :Demo WHERE Card_ID = :Card_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Demo', $demoData, PDO::PARAM_LOB);
$stmt->bindParam(':Card_ID', $cardId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
