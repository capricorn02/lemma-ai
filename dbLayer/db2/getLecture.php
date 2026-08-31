<?php
	//  getLecture: возвращает данные Лекции: Lecture_ID, Title, Notes, Controls, Scenario_ID, Is_Ready как объект JSON
	require_once 'reconnectDB.php'; 
	$lectureID = $_GET['ID'];
  
	$Q = "SELECT Title, Notes, Controls, Scenario_ID, Is_Ready FROM lecture WHERE Lecture_ID = ?";
	$stmt = $pdo -> prepare($Q);
	$stmt -> bindParam (1, $lectureID);  
	$stmt -> execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC); 

	$lecture = new class{};
	$lecture -> Lecture_ID = $lectureID;
	$lecture -> Title = $result['Title'];
	$lecture -> Notes = $result['Notes'];
	$lecture -> Controls = $result['Controls'];
	$lecture -> Scenario_ID = $result['Scenario_ID'];	
	$lecture -> Is_Ready = $result['Is_Ready'];	
 
  echo (json_encode ($lecture));	
?>	

