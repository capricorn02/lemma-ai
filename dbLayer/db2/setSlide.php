<?php
	require_once 'reconnectDB.php'; 
	$slide = json_decode ($_POST['Slide']);
	$slideID = $slide->Slide_ID;

	$SQL = "UPDATE slide SET Title = :Title, Notes = :Notes WHERE Slide_ID = :Slide_ID;";
	$stmt = $pdo -> prepare ($SQL);
	$stmt -> bindParam(':Title', $slide->Title);  
	$stmt -> bindParam(':Notes', $slide->Notes);
	$stmt -> bindParam(':Slide_ID', $slideID);
	if ($stmt -> execute()) {
		echo true;
	} else {
		echo false;
	}
?>
