<?php
	require_once 'reconnectDB.php'; 
	$cardID = $_GET['ID'];  
	$SQL = "SELECT Title, Notes FROM card WHERE Card_ID = :Card_ID";
  $stmt = $pdo -> prepare($SQL);
	$stmt -> bindParam (':Card_ID', $cardID);  
	if ($stmt -> execute()) {
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$card = new class{};
		$card -> Card_ID = $cardID;
		$card -> Title = $result['Title'];
		$card -> Notes = $result['Notes'];
		echo (json_encode ($card));
	} else {
		echo false;
	}
?>	

