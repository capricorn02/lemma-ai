<?php
// dbLayer/dbMML/getLectureSections.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$lectureId = $_GET['ID'] ?? $_GET['Lecture_ID'] ?? null;
if (!$lectureId) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT Section_ID, Name, Notes, Lecture_ID, Order_Num, DemoType_ID, Commands 
        FROM sections 
        WHERE Lecture_ID = :Lecture_ID 
        ORDER BY Order_Num ASC";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Lecture_ID', $lectureId, PDO::PARAM_INT);

if ($stmt->execute()) {
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $result = [];
    foreach ($rows as $row) {
        if (!empty($row['Commands'])) {
            $decoded = json_decode($row['Commands'], true);
            $row['Commands'] = ($decoded !== null) ? $decoded : $row['Commands'];
        } else {
            $row['Commands'] = null;
        }
        $result[] = $row;
    }
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
