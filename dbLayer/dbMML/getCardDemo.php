<?php
// dbLayer/dbMML/getCardDemo.php
require_once __DIR__ . '/reconnectDB_MML.php';

$cardId = $_GET['ID'] ?? $_GET['Card_ID'] ?? null;
if (!$cardId) {
    http_response_code(400);
    exit('Missing ID');
}

$sql = "SELECT Demo FROM cards WHERE Card_ID = :Card_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Card_ID', $cardId, PDO::PARAM_INT);
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
