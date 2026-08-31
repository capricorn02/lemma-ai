<?php
	require_once 'reconnectDB.php'; 
	$title = $_POST['Title'];
	$Q = "INSERT INTO folder (Title) VALUES (:Title)";
	$stmt = $pdo -> prepare ($Q);
	$stmt -> bindParam(':Title', $title);
	if ($stmt -> execute()) {
		$ID = $pdo -> lastInsertId ();
		$folder = new class{};
		$folder -> Folder_ID = $ID;
		$folder -> Title = $title;
		$folder -> Notes = '';
		echo (json_encode ($folder));	
	} else {
		echo false;
	}	
?>