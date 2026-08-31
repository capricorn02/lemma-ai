<?php
// dbLayer/dbMML/addCard2Scenario.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$cardId = $_POST['Card_ID'] ?? $_POST['CardID'] ?? null;
$scenarioId = $_POST['Scenario_ID'] ?? $_POST['ScenarioID'] ?? null;

if (!$cardId || !$scenarioId) {
    echo json_encode(false);
    exit;
}

// 1. Извлекаем данные карточки
$sqlCard = "SELECT Name, Notes, Demo, DemoType_ID FROM cards WHERE Card_ID = :Card_ID";
$stmtCard = $pdo->prepare($sqlCard);
$stmtCard->bindParam(':Card_ID', $cardId, PDO::PARAM_INT);

if (!$stmtCard->execute()) {
    echo json_encode(false);
    exit;
}

$cardData = $stmtCard->fetch(PDO::FETCH_ASSOC);
if (!$cardData) {
    echo json_encode(false);
    exit;
}

// 2. Вычисляем следующий порядковый номер Order_Num в сценарии
$sqlMax = "SELECT COALESCE(MAX(Order_Num), 0) AS max_order FROM slides WHERE Scenario_ID = :Scenario_ID";
$stmtMax = $pdo->prepare($sqlMax);
$stmtMax->bindParam(':Scenario_ID', $scenarioId, PDO::PARAM_INT);
$stmtMax->execute();
$rowMax = $stmtMax->fetch(PDO::FETCH_ASSOC);
$nextOrder = ((int)$rowMax['max_order']) + 1;

// 3. Создаем независимую глубокую копию Слайда в Сценарии
$sqlInsert = "INSERT INTO slides (Name, Notes, Scenario_ID, Order_Num, Demo, DemoType_ID) 
              VALUES (:Name, :Notes, :Scenario_ID, :Order_Num, :Demo, :DemoType_ID)";
$stmtInsert = $pdo->prepare($sqlInsert);
$stmtInsert->bindParam(':Name', $cardData['Name']);
$stmtInsert->bindParam(':Notes', $cardData['Notes']);
$stmtInsert->bindParam(':Scenario_ID', $scenarioId, PDO::PARAM_INT);
$stmtInsert->bindParam(':Order_Num', $nextOrder, PDO::PARAM_INT);
$stmtInsert->bindParam(':Demo', $cardData['Demo'], PDO::PARAM_LOB);
$stmtInsert->bindParam(':DemoType_ID', $cardData['DemoType_ID'], $cardData['DemoType_ID'] ? PDO::PARAM_INT : PDO::PARAM_NULL);

if ($stmtInsert->execute()) {
    $newSlideId = (int)$pdo->lastInsertId();
    echo json_encode($newSlideId);
} else {
    echo json_encode(false);
}
?>
