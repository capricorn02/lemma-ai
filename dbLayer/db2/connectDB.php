<?php
// Устанавливаем соединение с сервером
session_start();
$DB_HOST = $_GET['DB_HOST'];
$DB_NAME = $_GET['DB_NAME'];
$DB_USER = $_GET['DB_USER'];
$DB_PASSWORD = $_GET['DB_PASSWORD'];

$pdo = new PDO("mysql:host=" . $DB_HOST . "; dbname=" . $DB_NAME, $DB_USER, $DB_PASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT ); 
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$_SESSION['DB_HOST'] = $DB_HOST ;
$_SESSION['DB_NAME'] = $DB_NAME;
$_SESSION['DB_USER'] = $DB_USER ;
$_SESSION['DB_PASSWORD'] = $DB_PASSWORD;
?>