<?php
	require_once 'reconnectDB.php'; 
	$cardID = $_POST['CardID'];
	$scenarioID = $_POST['ScenarioID'];

	$SQL1 = "SELECT Title, Notes, Body, BodyType_ID FROM card WHERE Card_ID = :Card_ID";
	$stmt = $pdo -> prepare ($SQL1);
	$stmt -> bindParam(':Card_ID', $cardID);
	if ($stmt -> execute()) {
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$title = $result['Title'];
		$notes = $result['Notes'];
		$body = $result['Body'];
		$bodyTypeID = $result['BodyType_ID'];
	}
	
	$SQL2 = "SELECT MAX(Order_NUM) as max FROM slide INNER JOIN scenario USING (Scenario_ID) WHERE Scenario_ID = :Scenario_ID";
	$stmt = $pdo -> prepare ($SQL2);
	$stmt -> bindParam(':Scenario_ID', $scenarioID);
	if ($stmt -> execute()) {
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($result) {
			$num = $result['max'] + 1;	
		} else {
			$num = 1;
		} 		
	}
	
	$SQL3 = "INSERT INTO slide (Title, Notes, Body, DemoType_ID, Scenario_ID, Order_NUM) VALUES (:Title, :Notes, :Body, :DemoType_ID, :Scenario_ID, :Order_NUM)";
	$stmt = $pdo -> prepare ($SQL3);
	$stmt -> bindParam(':Title', $title);
	$stmt -> bindParam(':Notes', $notes);
	$stmt -> bindParam(':Body', $body);
	$stmt -> bindParam(':DemoType_ID', $bodyTypeID);
	$stmt -> bindParam(':Scenario_ID', $scenarioID);
	$stmt -> bindParam(':Order_NUM', $num);
	if ($stmt -> execute()) {
		echo $pdo -> lastInsertId();
	} else {
		echo false;
	}
?>