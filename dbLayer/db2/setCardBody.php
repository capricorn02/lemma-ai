<?php
require_once 'reconnectDB.php';
$cardID = $_POST['ID']; 
$bodyFile = $_FILES['Body'];
$body = file_get_contents($bodyFile['tmp_name']);

$SQL = "UPDATE card SET Body = :Body WHERE Card_ID = :Card_ID";

$stmt = $pdo -> prepare ($SQL);
$stmt -> bindParam(':Body', $body);  
$stmt -> bindParam(':Card_ID', $cardID);

if ($stmt -> execute()) {
	echo (true);
} else {
	echo (false);
}
?>