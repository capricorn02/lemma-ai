<?php
require_once 'reconnectDB.php';

$sectionID = $_POST['ID']; 
$controls = $_POST['Controls'];
$Q = "UPDATE Section SET Controls = :Controls WHERE Section_ID = :Section_ID";
$stmt = $pdo -> prepare ($Q);
$stmt -> bindParam(':Controls', $controls);  
$stmt -> bindParam(':Section_ID', $sectionID);

if ($stmt -> execute()) {
	echo (true);
} else {
	echo (false);	
}
?>