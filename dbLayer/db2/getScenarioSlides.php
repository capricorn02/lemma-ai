<?php
	require_once 'reconnectDB.php'; 
	$scenarioID = $_GET['ID'];  
	$Q = "SELECT  Slide_ID, Title, Notes, DemoType_ID, Scenario_ID, Order_NUM FROM Slide WHERE Scenario_ID = ? ORDER BY Order_NUM";
	$stmt = $pdo -> prepare($Q);
	$stmt -> bindParam (1, $scenarioID);  
	$stmt -> execute();
	$Arr=[]; 

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$elem = new class{};
		$elem -> Slide_ID = $row['Slide_ID'];
		$elem -> Title = $row['Title'];
		$elem -> Notes = $row['Notes'];
		$elem -> DemoType_ID = $row['DemoType_ID'];
		$elem -> Scenario_ID = $row['Scenario_ID'];
		$elem -> Order_NUM = $row['Order_NUM'];
		Array_push ($Arr, $elem);		
	} 
  echo (json_encode ($Arr));	
?>	
