<?php
	require_once 'reconnectDB.php'; 
	$SQL = "SELECT  Lecture_ID, Title, Notes FROM Lecture ORDER BY Lecture_ID";
	$stmt = $pdo -> prepare($SQL);
	$stmt -> execute();
	$Arr=[]; 

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$elem = new class{};
		$elem -> Lecture_ID = $row['Lecture_ID'];
		$elem -> Title = $row['Title'];
		$elem -> Notes = $row['Notes'];
		Array_push ($Arr, $elem);		
	} 
  echo (json_encode ($Arr));	
?>
