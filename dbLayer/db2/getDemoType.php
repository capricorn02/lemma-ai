<?php
	require_once 'reconnectDB.php'; 
	$typeID = $_GET['ID'];  
	$SQL = "SELECT DemoType_ID, Code, Title, Notes, URL FROM demotype WHERE DemoType_ID = :DemoType_ID";
  $stmt = $pdo -> prepare($SQL);
	$stmt -> bindParam (':DemoType_ID', $typeID);  
	if ($stmt -> execute()) {
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$obj = new class{};
		$obj -> DemoType_ID = $typeID;
		$obj -> Title = $result['Title'];
		$obj -> Notes = $result['Notes'];
		$obj -> URL = $result['URL'];
		echo (json_encode ($obj));
	} else {
		echo false;
	}
?>	

