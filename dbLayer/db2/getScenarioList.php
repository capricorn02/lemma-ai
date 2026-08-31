<?php
	require_once 'reconnectDB.php'; 
	$SQL = "SELECT Scenario_ID, Title, Notes FROM scenario ORDER BY Scenario_ID";
	$stmt = $pdo -> prepare($SQL);
	$stmt -> execute();
	$arr=[]; 

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$elem = new class{};
		$elem -> Scenario_ID = $row['Scenario_ID'];
		$elem -> Title = $row['Title'];
		$elem -> Notes = $row['Notes'];
		Array_push ($arr, $elem);		
	} 
  echo (json_encode ($arr));	
?>
