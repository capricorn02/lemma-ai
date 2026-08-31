<?php
// dbLayer/dbMML/getDemoTypeList.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$sql = "SELECT DemoType_ID, Name, Notes, URL FROM demotypes ORDER BY DemoType_ID ASC";
$stmt = $pdo->prepare($sql);

if ($stmt->execute()) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result ?: [], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
