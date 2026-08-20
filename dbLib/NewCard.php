<?php
require_once 'reconnectDB.php'; 
$name = $_POST['Name'] ?? 'Новый слайд';
$SQL = "INSERT INTO cards (title) VALUES (:Name)";
$stmt = $pdo->prepare($SQL);
$stmt->bindParam(':Name', $name);
if ($stmt->execute()) {
    $ID = $pdo->lastInsertId();
    $card = new class{};
    $card->Card_ID = $ID;
    $card->Title = $name;
    echo json_encode($card, JSON_UNESCAPED_UNICODE);	
} else {
    echo false;
}