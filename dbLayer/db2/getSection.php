<?php
	require_once 'reconnectDB.php'; 
	$sectionID = $_GET['ID'];
  
	$Q = "SELECT s.Title, s.Notes, DemoType_ID, d.URL, Controls, Lecture_ID, Order_Num, Quiz FROM section s INNER JOIN demotype d USING (DemoType_ID) WHERE Section_ID = ?";
	$stmt = $pdo -> prepare($Q);
	$stmt -> bindParam (1, $sectionID);  
	$stmt -> execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC); 

	$section = new class{};
	$section -> Section_ID = $sectionID;
	$section -> Title = $result['Title'];
	$section -> Notes = $result['Notes'];
	$section -> DemoType_ID = $result['DemoType_ID']; 
	$section -> Controls = $result['Controls'];
	$section -> Lecture_ID = $result['Lecture_ID'];	
	$section -> Order_Num = $result['Order_Num'];
	$section -> URL = $result['URL'];
		if ($result['Quiz']) {
			$section -> Quiz = json_decode ($result['Quiz']);
		} else {
			$section -> Quiz = null;
		};	
		if ($result['Controls']) {
			$section -> Controls = json_decode ($result['Controls']);
		} else {
			$section -> Controls = null;
		};	
 
  echo (json_encode ($section));	
?>	

