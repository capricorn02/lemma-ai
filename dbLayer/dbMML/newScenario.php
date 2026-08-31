<?php
// dbLayer/dbMML/newScenario.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$name = $_POST['Name'] ?? $_POST['Title'] ?? 'Новый сценарий';
$notes = $_POST['Notes'] ?? '';

$sql = "INSERT INTO scenarios (Name, Notes) VALUES (:Name, :Notes)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);

if ($stmt->execute()) {
    $id = (int)$pdo->lastInsertId();
    $scenario = [
        'Scenario_ID' => $id,
        'Name' => $name,
        'Notes' => $notes
    ];
    echo json_encode($scenario, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
