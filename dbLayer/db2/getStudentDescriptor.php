<?php
	require_once 'reconnectDB.php'; 
	$login = $_GET['Login'];
  
	$Q = "SELECT Descriptor FROM student WHERE Login = :Login";
	$stmt = $pdo -> prepare($Q);
	$stmt -> bindParam (':Login', $login);  
	$stmt -> execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC); 
 
  echo ($result['Descriptor']);	
?>	

