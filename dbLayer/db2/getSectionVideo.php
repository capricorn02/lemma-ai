<?php
header('Content-Type: video/mpeg');
require_once 'reconnectDB.php'; 
$ID = $_GET['ID'];

$sql = "SELECT Video FROM Section WHERE Section_ID=$ID";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo ($row['Video']);      
?>
