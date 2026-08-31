<?php
	require_once 'reconnectDB.php'; 
	$title = $_POST['Title'];
	$SQL = "INSERT INTO scenario (Title) VALUES (:Title)";
	$stmt = $pdo -> prepare ($SQL);
	$stmt -> bindParam(':Title', $title);
	if ($stmt -> execute()) {
		$ID = $pdo -> lastInsertId ();
		$scenario = new class{};
		$scenario -> Scenario_ID = $ID;
		$scenario -> Title = $title;
		$scenario -> Notes = '';		
		echo (json_encode ($scenario));	
	} else {
		echo false;
	}	
?>
