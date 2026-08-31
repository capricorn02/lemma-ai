<?php
	require_once 'reconnectDB.php'; 
	$slideID = $_GET['ID'];
  
	$Q = "SELECT Slide_ID, Title, Notes, DemoType_ID, Scenario_ID, Order_Num FROM slide WHERE Slide_ID = ?";
	$stmt = $pdo -> prepare($Q);
	$stmt -> bindParam (1, $slideID);  
	$stmt -> execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC); 

	$section = new class{};
	$section -> Slide_ID = $slideID;
	$section -> Title = $result['Title'];
	$section -> Notes = $result['Notes'];
	$section -> DemoType_ID = $result['DemoType_ID']; 
	$section -> Scenario_ID = $result['Scenario_ID'];	
	$section -> Order_Num = $result['Order_Num'];
 
  echo (json_encode ($section));	
?>	

