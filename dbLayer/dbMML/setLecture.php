<?php
// dbLayer/dbMML/setLecture.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

if (isset($_POST['Lecture'])) {
    $lecture = is_string($_POST['Lecture']) ? json_decode($_POST['Lecture'], true) : $_POST['Lecture'];
    $lectureId = $lecture['Lecture_ID'] ?? null;
    $name = $lecture['Name'] ?? $lecture['Title'] ?? '';
    $notes = $lecture['Notes'] ?? '';
} else {
    $lectureId = $_POST['Lecture_ID'] ?? $_POST['ID'] ?? null;
    $name = $_POST['Name'] ?? $_POST['Title'] ?? '';
    $notes = $_POST['Notes'] ?? '';
}

if (!$lectureId) {
    echo json_encode(false);
    exit;
}

$sql = "UPDATE lectures SET Name = :Name, Notes = :Notes WHERE Lecture_ID = :Lecture_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);
$stmt->bindParam(':Lecture_ID', $lectureId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
