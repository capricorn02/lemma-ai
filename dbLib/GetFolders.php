<?php
require_once 'reconnectDB.php';
$stmt = $pdo->query("SELECT id, title FROM folders ORDER BY id DESC");
echo json_encode($stmt->fetchAll() ?: [], JSON_UNESCAPED_UNICODE);
exit;