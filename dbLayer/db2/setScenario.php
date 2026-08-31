<?php
	require_once 'reconnectDB.php'; 
	$scenario = json_decode ($_POST['Scenario']);
	$scenarioID = $scenario->Scenario_ID;

	$SQL = "UPDATE scenario SET Title = :Title, Notes = :Notes WHERE Scenario_ID = :Scenario_ID;";
	$stmt = $pdo -> prepare ($SQL);
	$stmt -> bindParam(':Title', $scenario->Title);  
	$stmt -> bindParam(':Notes', $scenario->Notes);
	$stmt -> bindParam(':Scenario_ID', $scenarioID);	
	if ($stmt -> execute()) {		
		echo true;
	} else {
		echo false;
	}
?>