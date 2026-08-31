<?php
require_once 'reconnectDB.php';

$login = $_POST['Login']; 
$BodyFile = $_FILES['Body'];
$Body = file_get_contents($BodyFile['tmp_name']);

$Q = "UPDATE Student SET Photo = :Photo WHERE Login = :Login";
$stmt = $pdo -> prepare ($Q);
$stmt -> bindParam(':Photo', $Body);  
$stmt -> bindParam(':Login', $login);

if ($stmt -> execute()) {
	echo (true);
} else {
	echo (false);
}
?>