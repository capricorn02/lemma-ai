<?php
require_once 'reconnectDB.php'; 
$scenarioID = $_GET['ID'];

$Q1 = "DELETE FROM slide WHERE Scenario_ID = ?"; 
$stmt = $pdo -> prepare($Q1);
$stmt -> bindParam (1, $scenarioID);  
if (!($stmt -> execute())) {
	echo false;
	return;
}

$Q2 = "DELETE FROM scenario WHERE Scenario_ID = ?";
$stmt = $pdo -> prepare($Q2);
$stmt -> bindParam (1, $scenarioID);  
if (!$stmt -> execute()) {
	echo false;
} else {	
	echo true;
}
?>	

