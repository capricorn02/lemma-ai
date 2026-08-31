<?php
	require_once 'reconnectDB.php'; 
	$folder = json_decode ($_POST['Folder']);
	$folderID = $folder->Folder_ID;

	$SQL = "UPDATE folder SET Title = :Title, Notes = :Notes WHERE Folder_ID = :Folder_ID;";
	$stmt = $pdo -> prepare ($SQL);
	$stmt -> bindParam(':Title', $folder->Title);  
	$stmt -> bindParam(':Notes', $folder->Notes);
	$stmt -> bindParam(':Folder_ID', $folderID);	
	if ($stmt -> execute()) {
		echo true;
	} else {
		echo false;
	}
?>