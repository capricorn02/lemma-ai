<?php
// dbLayer/dbMML/setSectionCommands.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$sectionId = $_POST['ID'] ?? $_POST['Section_ID'] ?? null;
$commands = $_POST['Commands'] ?? $_POST['Controls'] ?? null;

if (!$sectionId) {
    echo json_encode(false);
    exit;
}

if (is_array($commands) || is_object($commands)) {
    $commandsStr = json_encode($commands, JSON_UNESCAPED_UNICODE);
} else {
    $commandsStr = (string)$commands;
}

$sql = "UPDATE sections SET Commands = :Commands WHERE Section_ID = :Section_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Commands', $commandsStr);
$stmt->bindParam(':Section_ID', $sectionId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
