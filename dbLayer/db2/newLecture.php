<?php
	require_once 'reconnectDB.php'; 
	$title = $_POST['Title'];
	$SQL = "INSERT INTO lecture (Title, Notes) VALUES (:Title, '')";
	$stmt = $pdo -> prepare ($SQL);
	$stmt -> bindParam(':Title', $title);
	if ($stmt -> execute()) {
		$ID = $pdo -> lastInsertId ();
		$lecture = new class{};
		$lecture -> Lecture_ID = $ID;
//		$lecture -> Scenario_ID = $ID;
		$lecture -> Title = $title;
		$lecture -> Notes = '';		
		echo (json_encode ($lecture));	
	} else {
		echo false;
	}	
?>
