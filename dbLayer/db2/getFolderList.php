<?php
	require_once 'reconnectDB.php'; 
	$SQL = "SELECT Folder_ID, Title, Notes FROM Folder ORDER BY Folder_ID";
	$stmt = $pdo -> prepare($SQL);
	$stmt -> execute();
	$Arr=[]; 

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$elem = new class{};
		$elem -> Folder_ID = $row['Folder_ID'];
		$elem -> Title = $row['Title'];
		$elem -> Notes = $row['Notes'];
		Array_push ($Arr, $elem);		
	} 
  echo (json_encode ($Arr));	
?>
