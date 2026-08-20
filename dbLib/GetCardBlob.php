<?php
require_once 'reconnectDB.php'; 
$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT body, mime_type FROM cards WHERE id = ?");
$stmt->execute([$id]);
$card = $stmt->fetch();
if ($card) {
    header("Content-Type: " . $card['mime_type']);
    echo $card['body'];
}
exit;