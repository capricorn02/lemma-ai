<?php
require_once 'reconnectDB.php';

$sectionID = $_POST['ID']; 
$VideoFile = $_FILES['Video'];
$Video = file_get_contents($VideoFile['tmp_name']);

$setVideoQ = "UPDATE Section SET Video = :Video WHERE Section_ID = :Section_ID";
$stmt = $pdo -> prepare ($setVideoQ);
$stmt -> bindParam(':Video', $Video);  
$stmt -> bindParam(':Section_ID', $sectionID);

if ($stmt -> execute()) {
	echo (true);
} else {
	echo (false);	
}
?>