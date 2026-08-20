<?php
session_start();
$params = $_SESSION['db_connection_params'];
$pdo = new PDO($params['dsn'], $params['username'], $params['password'], $params['options']);