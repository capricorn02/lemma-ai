<?php
// dbLayer/dbMML/getSlideDemo.php
require_once __DIR__ . '/reconnectDB_MML.php';

$slideId = $_GET['ID'] ?? $_GET['Slide_ID'] ?? null;
if (!$slideId) {
    http_response_code(400);
    exit('Missing ID');
}

$sql = "SELECT Demo FROM slides WHERE Slide_ID = :Slide_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Slide_ID', $slideId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && $row['Demo']) {
    $file = $row['Demo'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($file) ?: 'application/octet-stream';
    header('Content-Type: ' . $mime);
    echo $file;
} else {
    http_response_code(404);
    exit('Demo not found');
}
?>
