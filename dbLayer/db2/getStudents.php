<?php
	require_once 'reconnectDB.php'; 
	$SQL = "SELECT  Login, First_Name, Last_Name FROM Student ORDER BY Login";
	$stmt = $pdo -> prepare($SQL);
	$stmt -> execute();
	$Arr=[]; 

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		$elem = new class{};
		$elem -> Login = $row['Login'];
		$elem -> First_Name = $row['First_Name'];
		$elem -> Last_Name = $row['Last_Name'];
		Array_push ($Arr, $elem);		
	} 
  echo (json_encode ($Arr));	
?>
