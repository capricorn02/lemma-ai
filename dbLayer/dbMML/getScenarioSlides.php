<?php
// dbLayer/dbMML/getScenarioSlides.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$scenarioId = $_GET['ID'] ?? $_GET['Scenario_ID'] ?? null;
if (!$scenarioId) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT Slide_ID, Name, Notes, Scenario_ID, Order_Num, DemoType_ID 
        FROM slides 
        WHERE Scenario_ID = :Scenario_ID 
        ORDER BY Order_Num ASC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Scenario_ID', $scenarioId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result ?: [], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
