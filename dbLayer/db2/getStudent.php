<?php
	require_once 'reconnectDB.php';
	$login = $_GET['Login'];
	$password = $_GET['Password'];
  
	$SQL = "SELECT Password, Last_Name, First_Name FROM student  WHERE Login = ? ";
	$stmt = $pdo -> prepare($SQL);
  $stmt -> bindParam (1, $login);  
	$stmt -> execute();
	
	$SQL = "SELECT Password, Last_Name, First_Name FROM student  WHERE Login = ? ";
	$stmt = $pdo -> prepare($SQL);
 	$stmt -> bindParam (1, $login);  
	$stmt -> execute();
	
	if (($stmt -> rowCount())==1)  {
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
//	var_dump($res);
		if ($result['Password'] != $password) {
			$user = false;
			echo (json_encode ($user));
			return;
		}
		$user = new class{};
		$user -> Login = $login;
		$user -> First_Name = $result['First_Name'];
		$user -> Last_Name = $result['Last_Name'];
	} else {
		$user = false;
	}	;	
	echo (json_encode ($user));	
?>	

