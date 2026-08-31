<?php
//  getResults: возвращает Результаты изучения Студентом login Декции ID: массив Статусов на каждую секцию (1 - прослушана, 2 - усвоена)
//  если хотя бы один результат отсутствует, то возврвщвется NULL
//	eсли резултатов несколько, то возвращается результать с наибольшим значением Ingress_ID 
	require_once 'reconnectDB.php'; 
	$lectureID = $_GET['ID'];
	$login = $_GET['Login'];
  
	$ingressCountQ = "SELECT Count(*) AS Cnt FROM Ingress WHERE Lecture_ID = ? AND Login = ?";
	$stmt = $pdo -> prepare($ingressCountQ);
	$stmt -> bindParam (1, $lectureID);  
	$stmt -> bindParam (2, $login);  
	$stmt -> execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);	
	$count = $result['Cnt'];

	if ($count == 0) {
		$results = false;
	} else {
		$resultsQ = "SELECT Results FROM Ingress WHERE Lecture_ID = ? AND Login = ? ORDER BY Ingress_ID DESC LIMIT 1";
		$stmt = $pdo -> prepare($resultsQ);
		$stmt -> bindParam (1, $lectureID);  
		$stmt -> bindParam (2, $login);  
		$stmt -> execute();
		$res = $stmt->fetch(PDO::FETCH_ASSOC); 

		$results = $res['Results'];
	}
  echo (json_encode ($results));	
?>	

