<?php
require_once 'reconnectDB.php'; 
$lectureID = $_GET['ID'];

$Q1 = "DELETE FROM section WHERE Lecture_ID = ?"; 
$stmt = $pdo -> prepare($Q1);
$stmt -> bindParam (1, $lectureID);  
if (!($stmt -> execute())) {
	echo false;
	return;
}
//$result = $stmt->fetch(PDO::FETCH_ASSOC);	

$Q2 = "DELETE FROM lecture WHERE Lecture_ID = ?";
$stmt = $pdo -> prepare($Q2);
$stmt -> bindParam (1, $lectureID);  
if (!$stmt -> execute()) {
	echo false;
} else {	
	echo true;
}
?>	

