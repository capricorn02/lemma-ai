<?php
// dbLayer/dbMML/delLecture.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$lectureId = $_GET['ID'] ?? $_POST['ID'] ?? $_POST['Lecture_ID'] ?? null;
if (!$lectureId) {
    echo json_encode(false);
    exit;
}

$sql = "DELETE FROM lectures WHERE Lecture_ID = :Lecture_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Lecture_ID', $lectureId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
