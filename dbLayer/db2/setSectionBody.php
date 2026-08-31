<?php
require_once 'reconnectDB.php';
//echo '*';
$sectionID = $_POST['ID'];
// echo $sectionID;
$BodyFile = $_FILES['Body'];
$Body = file_get_contents($BodyFile['tmp_name']);

$Q = "UPDATE Section SET Body = :Body WHERE Section_ID = :Section_ID";
$stmt = $pdo -> prepare ($Q);
$stmt -> bindParam(':Body', $Body);  
$stmt -> bindParam(':Section_ID', $sectionID);

if ($stmt -> execute()) {
	echo (true);
} else {
	echo (false);
}
?>
