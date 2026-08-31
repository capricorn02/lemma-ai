<?php
// dbLayer/dbMML/delSlide.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$slideId = $_GET['ID'] ?? $_POST['ID'] ?? $_POST['Slide_ID'] ?? null;
if (!$slideId) {
    echo json_encode(false);
    exit;
}

$sql = "DELETE FROM slides WHERE Slide_ID = :Slide_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Slide_ID', $slideId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
