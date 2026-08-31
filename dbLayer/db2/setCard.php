<?php
	require_once 'reconnectDB.php'; 
	$card = json_decode ($_POST['Card']);
	$cardID = $card->Card_ID;

	$SQL = "UPDATE card SET Title = :Title, Notes = :Notes, BodyType_ID = :BodyType_ID WHERE Card_ID = :Card_ID;";
	$stmt = $pdo -> prepare ($SQL);
	$stmt -> bindParam(':Title', $card->Title);  
	$stmt -> bindParam(':Notes', $card->Notes);
	$stmt -> bindParam(':BodyType_ID', $card->BodyType_ID);	
	$stmt -> bindParam(':Card_ID', $cardID);	
	if ($stmt -> execute()) {
		echo true;
	} else {
		echo false;
	}
?>