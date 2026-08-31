<?php
// dbLayer/dbMML/getLecture.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$lectureId = $_GET['ID'] ?? $_GET['Lecture_ID'] ?? null;
if (!$lectureId) {
    echo json_encode(false);
    exit;
}

$sql = "SELECT Lecture_ID, Name, Notes FROM lectures WHERE Lecture_ID = :Lecture_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Lecture_ID', $lectureId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result ?: false, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
