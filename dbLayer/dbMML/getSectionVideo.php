<?php
// dbLayer/dbMML/getSectionVideo.php
require_once __DIR__ . '/reconnectDB_MML.php';

$sectionId = $_GET['ID'] ?? $_GET['Section_ID'] ?? null;
if (!$sectionId) {
    http_response_code(400);
    exit('Missing ID');
}

$sql = "SELECT Video FROM sections WHERE Section_ID = :Section_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Section_ID', $sectionId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row && $row['Video']) {
    $file = $row['Video'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($file) ?: 'video/webm';
    header('Content-Type: ' . $mime);
    echo $file;
} else {
    http_response_code(404);
    exit('Video not found');
}
?>
