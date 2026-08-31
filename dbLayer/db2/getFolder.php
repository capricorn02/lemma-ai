<?php
	require_once 'reconnectDB.php'; 
	$folderID = $_GET['ID'];
  
	$SQL = "SELECT Title, Notes FROM folder WHERE Folder_ID = :Folder_ID";
  $stmt = $pdo -> prepare($SQL);
	$stmt -> bindParam (':Folder_ID', $folderID);  
	if ($stmt -> execute()) {
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$folder = new class{};
		$folder -> Folder_ID = $folderID;
		$folder -> Title = $result['Title'];
		$folder -> Notes = $result['Notes'];
		echo (json_encode ($folder));
	} else {
		echo false;
	}
?>	

