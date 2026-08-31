<?php
// dbLayer/dbMML/setCard.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

if (isset($_POST['Card'])) {
    $card = is_string($_POST['Card']) ? json_decode($_POST['Card'], true) : $_POST['Card'];
    $cardId = $card['Card_ID'] ?? null;
    $name = $card['Name'] ?? $card['Title'] ?? '';
    $notes = $card['Notes'] ?? '';
    $demoTypeId = !empty($card['DemoType_ID']) ? (int)$card['DemoType_ID'] : null;
} else {
    $cardId = $_POST['Card_ID'] ?? $_POST['ID'] ?? null;
    $name = $_POST['Name'] ?? $_POST['Title'] ?? '';
    $notes = $_POST['Notes'] ?? '';
    $demoTypeId = !empty($_POST['DemoType_ID']) ? (int)$_POST['DemoType_ID'] : null;
}

if (!$cardId) {
    echo json_encode(false);
    exit;
}

$sql = "UPDATE cards SET Name = :Name, Notes = :Notes, DemoType_ID = :DemoType_ID WHERE Card_ID = :Card_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);
$stmt->bindParam(':DemoType_ID', $demoTypeId, $demoTypeId ? PDO::PARAM_INT : PDO::PARAM_NULL);
$stmt->bindParam(':Card_ID', $cardId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
