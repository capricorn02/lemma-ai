<?php
// dbLayer/dbMML/getDemoType.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$id = $_GET['ID'] ?? null;
if (!$id) {
    echo json_encode(false);
    exit;
}

$sql = "SELECT DemoType_ID, Name, Notes, URL FROM demotypes WHERE DemoType_ID = :DemoType_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':DemoType_ID', $id, PDO::PARAM_INT);

if ($stmt->execute()) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result ?: false, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
