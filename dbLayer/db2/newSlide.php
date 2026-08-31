<?php
	require_once 'reconnectDB.php'; 
	$title = $_POST['Title'];
	$scenarioID = $_POST['ID'];
	$SQL = "INSERT INTO slide (Title, Notes, Scenario_ID)  VALUES (:Title, '', :Scenario_ID)";
	$stmt = $pdo -> prepare ($SQL);
	$stmt -> bindParam(':Title', $title);
	$stmt -> bindParam(':Scenario_ID', $scenarioID);
	
	
	if ($stmt -> execute()) {
		$ID = $pdo -> lastInsertId ();
		$slide = new class{};
		$slide -> Slide_ID = $ID;
		$slide -> Scenario_ID = $ID;
		$slide -> Title = $title;
		$slide -> Notes = '';		
		echo (json_encode ($slide));	
	} else {
		echo false;
	}	
?>
