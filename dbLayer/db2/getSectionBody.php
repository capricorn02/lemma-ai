<?php
header('Content-Type: video/mpg');//!!! переписать на fileInfo !!!
require_once 'reconnectDB.php'; 
$ID = $_GET['ID'];

$sql = "SELECT Body FROM Section WHERE Section_ID=$ID";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo ($row['Body']);      
?>