<?php
// dbLayer/dbMML/getScenario.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$scenarioId = $_GET['ID'] ?? $_GET['Scenario_ID'] ?? null;
if (!$scenarioId) {
    echo json_encode(false);
    exit;
}

$sql = "SELECT Scenario_ID, Name, Notes FROM scenarios WHERE Scenario_ID = :Scenario_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Scenario_ID', $scenarioId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result ?: false, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
