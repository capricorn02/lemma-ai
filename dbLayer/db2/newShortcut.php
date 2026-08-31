<?php
	require_once 'reconnectDB.php'; 
	$cardID = $_POST['CardID'];
	$folderID = $_POST['FolderID'];
	$SQL = "INSERT INTO shortcut (Card_ID, Folder_ID) VALUES (:Card_ID, :Folder_ID)";
	$stmt = $pdo -> prepare ($SQL);
	$stmt -> bindParam(':Card_ID', $cardID);
	$stmt -> bindParam(':Folder_ID', $folderID);
	if ($stmt -> execute()) {
		echo $pdo -> lastInsertId();
	} else {
		echo false;
	}
?>
