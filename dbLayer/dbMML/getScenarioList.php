<?php
// dbLayer/dbMML/getScenarioList.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$sql = "SELECT Scenario_ID, Name, Notes FROM scenarios ORDER BY Scenario_ID DESC";
$stmt = $pdo->prepare($sql);

if ($stmt->execute()) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result ?: [], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
