<?php
	//  getStudent: возвращает данные Студента: Login, Last_Name, Frst_Name как объект JSON
	require_once 'reconnectDB.php'; 
	$Login = $_GET['Login'];
  
	$studentQ = "SELECT Last_Name, First_Name FROM Student WHERE Login = :Login";
	$stmt = $pdo -> prepare($studentQ);
	$stmt -> bindParam (':Login', $Login);  
	$stmt -> execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC); 

	$student = new class{};
	$student -> Login = $Login;
	$student -> Last_Name = $result['Last_Name'];
	$student -> First_Name = $result['First_Name'];
 
  echo (json_encode ($student));	
?>	

