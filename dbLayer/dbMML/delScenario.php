<?php
// dbLayer/dbMML/delScenario.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$scenarioId = $_GET['ID'] ?? $_POST['ID'] ?? $_POST['Scenario_ID'] ?? null;
if (!$scenarioId) {
    echo json_encode(false);
    exit;
}

$sql = "DELETE FROM scenarios WHERE Scenario_ID = :Scenario_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Scenario_ID', $scenarioId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
