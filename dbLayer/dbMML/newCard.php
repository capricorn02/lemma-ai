<?php
// dbLayer/dbMML/newCard.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$name = $_POST['Name'] ?? $_POST['Title'] ?? 'Новая карточка';
$notes = $_POST['Notes'] ?? '';
$demoTypeId = !empty($_POST['DemoType_ID']) ? (int)$_POST['DemoType_ID'] : null;

$sql = "INSERT INTO cards (Name, Notes, DemoType_ID) VALUES (:Name, :Notes, :DemoType_ID)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);
$stmt->bindParam(':DemoType_ID', $demoTypeId, $demoTypeId ? PDO::PARAM_INT : PDO::PARAM_NULL);

if ($stmt->execute()) {
    $id = (int)$pdo->lastInsertId();
    $card = [
        'Card_ID' => $id,
        'Name' => $name,
        'Notes' => $notes,
        'DemoType_ID' => $demoTypeId
    ];
    echo json_encode($card, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
