<?php
	require_once 'reconnectDB.php'; 
	$folderID = $_GET['ID'];
  
	$SQL = "SELECT  Card_ID, Title, Notes, BodyType_ID FROM shortcut INNER JOIN card USING(Card_ID) WHERE Folder_ID = :Folder_ID ORDER BY Card_ID";
	$stmt = $pdo -> prepare($SQL);
	$stmt -> bindParam (':Folder_ID', $folderID);  
	$stmt -> execute();
	$arr=[]; 

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$elem = new class{};
		$elem -> Card_ID = $row['Card_ID'];
		$elem -> Title = $row['Title'];
		$elem -> Notes = $row['Notes'];
		$elem -> BodyType_ID = $row['BodyType_ID'];
		Array_push ($arr, $elem);		
	} 
  echo (json_encode ($arr));	
?>	
