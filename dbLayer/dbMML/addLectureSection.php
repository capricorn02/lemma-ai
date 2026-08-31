<?php
// dbLayer/dbMML/addLectureSection.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

$lectureId = $_POST['Lecture_ID'] ?? $_POST['ID'] ?? null;
$name = $_POST['Name'] ?? $_POST['Title'] ?? 'Новый раздел';
$notes = $_POST['Notes'] ?? '';
$demoTypeId = !empty($_POST['DemoType_ID']) ? (int)$_POST['DemoType_ID'] : null;

if (!$lectureId) {
    echo json_encode(false);
    exit;
}

$sqlMax = "SELECT COALESCE(MAX(Order_Num), 0) AS max_order FROM sections WHERE Lecture_ID = :Lecture_ID";
$stmtMax = $pdo->prepare($sqlMax);
$stmtMax->bindParam(':Lecture_ID', $lectureId, PDO::PARAM_INT);
$stmtMax->execute();
$rowMax = $stmtMax->fetch(PDO::FETCH_ASSOC);
$nextOrder = ((int)$rowMax['max_order']) + 1;

$sql = "INSERT INTO sections (Name, Notes, Lecture_ID, Order_Num, DemoType_ID) 
        VALUES (:Name, :Notes, :Lecture_ID, :Order_Num, :DemoType_ID)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);
$stmt->bindParam(':Lecture_ID', $lectureId, PDO::PARAM_INT);
$stmt->bindParam(':Order_Num', $nextOrder, PDO::PARAM_INT);
$stmt->bindParam(':DemoType_ID', $demoTypeId, $demoTypeId ? PDO::PARAM_INT : PDO::PARAM_NULL);

if ($stmt->execute()) {
    $newId = (int)$pdo->lastInsertId();
    echo json_encode([
        'Section_ID' => $newId,
        'Name' => $name,
        'Notes' => $notes,
        'Lecture_ID' => (int)$lectureId,
        'Order_Num' => $nextOrder,
        'DemoType_ID' => $demoTypeId
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
