<?php
	require_once 'reconnectDB.php'; 
	$title = $_POST['Title'];
	$notes;
	$SQL = "INSERT INTO card (Title, Notes) VALUES (:Title, :Notes)";
	$stmt = $pdo -> prepare ($SQL);
	$stmt -> bindParam(':Title', $title);
	$stmt -> bindParam(':Notes', $notes);
	if ($stmt -> execute()) {
		$ID = $pdo -> lastInsertId ();
		$card = new class{};
		$card -> Card_ID = $ID;
		$card -> Title = $title;
		$card -> Notes = $notes;
		$card -> BodyType_ID = null;
		echo (json_encode ($card));	
	} else {
		echo false;
	}	
?>