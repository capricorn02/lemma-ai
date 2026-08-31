<?php
// dbLayer/dbMML/getSlide.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$slideId = $_GET['ID'] ?? $_GET['Slide_ID'] ?? null;
if (!$slideId) {
    echo json_encode(false);
    exit;
}

$sql = "SELECT Slide_ID, Name, Notes, Scenario_ID, Order_Num, DemoType_ID FROM slides WHERE Slide_ID = :Slide_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Slide_ID', $slideId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result ?: false, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
