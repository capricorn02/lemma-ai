<?php
// dbLayer/dbMML/getSectionDemo.php
require_once __DIR__ . '/reconnectDB_MML.php';

$sectionId = $_GET['ID'] ?? $_GET['Section_ID'] ?? null;
if (!$sectionId) {
    http_response_code(400);
    exit('Missing ID');
}

$sql = "SELECT Demo FROM sections WHERE Section_ID = :Section_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Section_ID', $sectionId, PDO::PARAM_INT);
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
