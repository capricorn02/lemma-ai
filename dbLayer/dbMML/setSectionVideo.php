<?php
// dbLayer/dbMML/setSectionVideo.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$sectionId = $_POST['ID'] ?? $_POST['Section_ID'] ?? null;
$videoFile = $_FILES['Video'] ?? null;

if (!$sectionId || !$videoFile || !is_uploaded_file($videoFile['tmp_name'])) {
    echo json_encode(false);
    exit;
}

$videoData = file_get_contents($videoFile['tmp_name']);

$sql = "UPDATE sections SET Video = :Video WHERE Section_ID = :Section_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Video', $videoData, PDO::PARAM_LOB);
$stmt->bindParam(':Section_ID', $sectionId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
