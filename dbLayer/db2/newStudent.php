<?php
require_once 'reconnectDB.php'; 
$studentJSON = $_POST['Student'];
$student = json_decode ($studentJSON);
$Q = "INSERT INTO Student (Login, Password, Last_Name, First_Name) VALUES (:Login, :Password, :Last_Name, :First_Name)";
$stmt = $pdo -> prepare ($Q);
$stmt -> bindParam(':Login', $student -> Login);
$stmt -> bindParam(':Password', $student -> Password);
$stmt -> bindParam(':Last_Name', $student -> Last_Name);
$stmt -> bindParam(':First_Name', $student -> First_Name);
$stmt -> execute();
$ID = $pdo -> lastInsertId (); 
echo $ID;
?>