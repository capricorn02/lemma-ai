<?php
	//	getLectureSections: возвращает данные Секций Лекции как массив объектов JSON
	require_once 'reconnectDB.php'; 
	$lectureID = $_GET['ID'];
  
	$Q = "SELECT Section_ID, Title, Notes, Controls, Lecture_ID, Order_NUM, DemoType_ID, Quiz FROM Section WHERE Lecture_ID = ? ORDER BY Order_NUM";
	$stmt = $pdo -> prepare($Q);
	$stmt -> bindParam (1, $lectureID);  
	$stmt -> execute();
	$Arr=[]; 

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$elem = new class{};
		$elem -> Section_ID = $row['Section_ID'];
		$elem -> Title = $row['Title'];
		$elem -> Notes = $row['Notes'];
		$elem -> DemoType_ID = $row['DemoType_ID'];
		if ($row['Controls']) {
			$elem -> Controls = json_decode ($row['Controls']);
		} else {
			$elem -> Controls = null;
		};		
		$elem -> Lecture_ID = $row['Lecture_ID'];
		$elem -> Order_NUM = $row['Order_NUM'];
		if ($row['Quiz']) {
			$elem -> Quiz = json_decode ($row['Quiz']);
		} else {
			$elem -> Quiz = null;
		};	
		Array_push ($Arr, $elem);		
	} 
  echo (json_encode ($Arr));	
?>	
