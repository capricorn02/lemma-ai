<?php
// dbLayer/dbMML/delSection.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$sectionId = $_GET['ID'] ?? $_POST['ID'] ?? $_POST['Section_ID'] ?? null;
if (!$sectionId) {
    echo json_encode(false);
    exit;
}

$sql = "DELETE FROM sections WHERE Section_ID = :Section_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Section_ID', $sectionId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
