<?php
// dbLayer/dbMML/setSectionDemo.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$sectionId = $_POST['ID'] ?? $_POST['Section_ID'] ?? null;
$demoFile = $_FILES['Demo'] ?? $_FILES['Body'] ?? null;

if (!$sectionId || !$demoFile || !is_uploaded_file($demoFile['tmp_name'])) {
    echo json_encode(false);
    exit;
}

$demoData = file_get_contents($demoFile['tmp_name']);

$sql = "UPDATE sections SET Demo = :Demo WHERE Section_ID = :Section_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Demo', $demoData, PDO::PARAM_LOB);
$stmt->bindParam(':Section_ID', $sectionId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
