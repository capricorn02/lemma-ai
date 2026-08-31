<?php
	require_once 'reconnectDB.php'; 
	$lectureID = $_POST['ID'];
	$title = $_POST['Title'];
	
	$Q1 = "SELECT MAX(Order_NUM) AS num FROM section WHERE Lecture_ID = :Lecture_ID";
	$stmt = $pdo -> prepare ($Q1);
	$stmt -> bindParam(':Lecture_ID', $lectureID);
	$stmt -> execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC); 
	$orderNUM = $result ['num'];
	if ($orderNUM > 0) {
		$orderNUM ++;
	} else {
		$orderNUM = 1;
	};

	$Q2 = "INSERT INTO section (Title, Notes, Lecture_ID, Order_NUM)  VALUES (:Title, '', :Lecture_ID, :Order_NUM)";
	$stmt = $pdo -> prepare ($Q2);
	$stmt -> bindParam(':Title', $title);
	$stmt -> bindParam(':Lecture_ID', $lectureID);
	$stmt -> bindParam(':Order_NUM', $orderNUM);

	
	if ($stmt -> execute()) {
		$ID = $pdo -> lastInsertId ();
		$section = new class{};
		$section -> Section_ID = $ID;
		$section -> Title = $title;
		$section -> Notes = '';		
		$section -> Lecture_ID = $lectureID;
		$section -> Order_NUM = $orderNUM;
		echo (json_encode ($section));	
	} else {
		echo false;
	}	
?>
