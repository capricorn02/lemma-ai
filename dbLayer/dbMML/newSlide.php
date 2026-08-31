<?php
// dbLayer/dbMML/newSlide.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

if (isset($_POST['Slide'])) {
    $slide = is_string($_POST['Slide']) ? json_decode($_POST['Slide'], true) : $_POST['Slide'];
    $name = $slide['Name'] ?? $slide['Title'] ?? 'Новый слайд';
    $notes = $slide['Notes'] ?? '';
    $scenarioId = (int)($slide['Scenario_ID'] ?? 0);
    $orderNum = (int)($slide['Order_Num'] ?? $slide['Order_NUM'] ?? 1);
    $demoTypeId = !empty($slide['DemoType_ID']) ? (int)$slide['DemoType_ID'] : null;
} else {
    $name = $_POST['Name'] ?? $_POST['Title'] ?? 'Новый слайд';
    $notes = $_POST['Notes'] ?? '';
    $scenarioId = (int)($_POST['Scenario_ID'] ?? 0);
    $orderNum = (int)($_POST['Order_Num'] ?? $_POST['Order_NUM'] ?? 1);
    $demoTypeId = !empty($_POST['DemoType_ID']) ? (int)$_POST['DemoType_ID'] : null;
}

if (!$scenarioId) {
    echo json_encode(false);
    exit;
}

$sql = "INSERT INTO slides (Name, Notes, Scenario_ID, Order_Num, DemoType_ID) 
        VALUES (:Name, :Notes, :Scenario_ID, :Order_Num, :DemoType_ID)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);
$stmt->bindParam(':Scenario_ID', $scenarioId, PDO::PARAM_INT);
$stmt->bindParam(':Order_Num', $orderNum, PDO::PARAM_INT);
$stmt->bindParam(':DemoType_ID', $demoTypeId, $demoTypeId ? PDO::PARAM_INT : PDO::PARAM_NULL);

if ($stmt->execute()) {
    $newId = (int)$pdo->lastInsertId();
    echo json_encode([
        'Slide_ID' => $newId,
        'Name' => $name,
        'Notes' => $notes,
        'Scenario_ID' => $scenarioId,
        'Order_Num' => $orderNum,
        'DemoType_ID' => $demoTypeId
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
