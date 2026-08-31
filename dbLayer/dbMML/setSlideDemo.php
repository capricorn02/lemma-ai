<?php
// dbLayer/dbMML/setSlideDemo.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$slideId = $_POST['ID'] ?? $_POST['Slide_ID'] ?? null;
$demoFile = $_FILES['Demo'] ?? $_FILES['Body'] ?? null;

if (!$slideId || !$demoFile || !is_uploaded_file($demoFile['tmp_name'])) {
    echo json_encode(false);
    exit;
}

$demoData = file_get_contents($demoFile['tmp_name']);

$sql = "UPDATE slides SET Demo = :Demo WHERE Slide_ID = :Slide_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Demo', $demoData, PDO::PARAM_LOB);
$stmt->bindParam(':Slide_ID', $slideId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
