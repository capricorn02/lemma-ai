<?php
// dbLayer/dbMML/getSection.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$sectionId = $_GET['ID'] ?? $_GET['Section_ID'] ?? null;
if (!$sectionId) {
    echo json_encode(false);
    exit;
}

$sql = "SELECT Section_ID, Name, Notes, Lecture_ID, Order_Num, DemoType_ID, Commands 
        FROM sections 
        WHERE Section_ID = :Section_ID";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Section_ID', $sectionId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        if (!empty($result['Commands'])) {
            $decoded = json_decode($result['Commands'], true);
            $result['Commands'] = ($decoded !== null) ? $decoded : $result['Commands'];
        } else {
            $result['Commands'] = null;
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(false);
    }
} else {
    echo json_encode(false);
}
?>
