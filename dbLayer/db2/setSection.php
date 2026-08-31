<?php
	require_once 'reconnectDB.php'; 
	$section = json_decode ($_POST['Section']);
	$sectionID = $section->Section_ID;

	$SQL = "UPDATE section SET Title = :Title, Notes = :Notes, DemoType_ID = :DemoType_ID,  WHERE Section_ID = :Section_ID;";
	$stmt = $pdo -> prepare ($SQL);
	$stmt -> bindParam(':Title', $section->Title);  
	$stmt -> bindParam(':Notes', $section->Notes);
	$stmt -> bindParam(':DemoType_ID', $section->DemoType_ID);
	$stmt -> bindParam(':Section_ID', $section_ID);
	
	if ($stmt -> execute()) {
		echo true;
	} else {
		echo false;
	}
?>
