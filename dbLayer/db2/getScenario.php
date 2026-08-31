<?php
	require_once 'reconnectDB.php'; 
	$scenarioID = $_GET['ID'];
  
	$SQL = "SELECT Title, Notes FROM scenario WHERE Scenario_ID = :Scenario_ID";
  $stmt = $pdo -> prepare($SQL);
	$stmt -> bindParam (':Scenario_ID', $scenarioID);  
	if ($stmt -> execute()) {
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$scenario = new class{};
		$scenario -> Scenario_ID = $scenarioID;
		$scenario -> Title = $result['Title'];
		$scenario -> Notes = $result['Notes'];
		echo (json_encode ($scenario));
	} else {
		echo false;
	}
?>	
