<?php
// dbLayer/dbMML/setScenario.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

if (isset($_POST['Scenario'])) {
    $scenario = is_string($_POST['Scenario']) ? json_decode($_POST['Scenario'], true) : $_POST['Scenario'];
    $scenarioId = $scenario['Scenario_ID'] ?? null;
    $name = $scenario['Name'] ?? $scenario['Title'] ?? '';
    $notes = $scenario['Notes'] ?? '';
} else {
    $scenarioId = $_POST['Scenario_ID'] ?? $_POST['ID'] ?? null;
    $name = $_POST['Name'] ?? $_POST['Title'] ?? '';
    $notes = $_POST['Notes'] ?? '';
}

if (!$scenarioId) {
    echo json_encode(false);
    exit;
}

$sql = "UPDATE scenarios SET Name = :Name, Notes = :Notes WHERE Scenario_ID = :Scenario_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);
$stmt->bindParam(':Scenario_ID', $scenarioId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
