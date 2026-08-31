<?php
require_once 'reconnectDB.php';
$login = $_POST['Login']; 
$descriptor = $_POST['Descriptor'];

$Q = "UPDATE Student SET Descriptor = :Descriptor WHERE Login = :Login";
$stmt = $pdo -> prepare ($Q);
$stmt -> bindParam(':Descriptor', $descriptor);  
$stmt -> bindParam(':Login', $login);

if ($stmt -> execute()) {
	echo (true);
} else {
	echo (false);	
}
?>