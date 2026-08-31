<?php
require_once 'reconnectDB.php'; 
$lecture = json_decode ($_POST['Lecture']);
$lectureID = $lecture->Lecture_ID;

$SQL = "UPDATE Lecture SET Title = :Title, Notes = :Notes, Controls = :Controls, Scenario_ID = :Scenario_ID, Is_Ready =:Is_Ready WHERE Lecture_ID = :Lecture_ID;";
$stmt = $pdo -> prepare ($SQL);
echo ($lecture->Title);
$stmt -> bindParam(':Title', $lecture->Title);  
$stmt -> bindParam(':Notes', $lecture->Notes);
$stmt -> bindParam(':Controls', $lecture->Controls);
$stmt -> bindParam(':Scenario_ID', $lecture->Scenario_ID);
$stmt -> bindParam(':Is_Ready', $lecture->Ready);
$stmt -> bindParam(':Lecture_ID', $lecture->Lecture_ID);
$stmt -> execute();
?>