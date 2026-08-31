<?php
	require_once 'reconnectDB.php'; 
	$cardID = $_GET['ID'];

	$SQL = "SELECT Body FROM Card WHERE Card_ID = :Card_ID";
	$stmt = $pdo->prepare($SQL);
	$stmt -> bindParam (':Card_ID', $cardID);  
	$stmt->execute();
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$file = $row['Body'];

	$finfo = new finfo(FILEINFO_MIME_TYPE);
 	$mime = $finfo->buffer($file);
	header('Content-Type: ' . $mime);
	echo $file;
?>
