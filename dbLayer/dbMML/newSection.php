<?php
// dbLayer/dbMML/newSection.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

if (isset($_POST['Section'])) {
    $section = is_string($_POST['Section']) ? json_decode($_POST['Section'], true) : $_POST['Section'];
    $name = $section['Name'] ?? $section['Title'] ?? 'Новый раздел';
    $notes = $section['Notes'] ?? '';
    $lectureId = (int)($section['Lecture_ID'] ?? 0);
    $orderNum = (int)($section['Order_Num'] ?? $section['Order_NUM'] ?? 1);
    $demoTypeId = !empty($section['DemoType_ID']) ? (int)$section['DemoType_ID'] : null;
} else {
    $name = $_POST['Name'] ?? $_POST['Title'] ?? 'Новый раздел';
    $notes = $_POST['Notes'] ?? '';
    $lectureId = (int)($_POST['Lecture_ID'] ?? 0);
    $orderNum = (int)($_POST['Order_Num'] ?? $_POST['Order_NUM'] ?? 1);
    $demoTypeId = !empty($_POST['DemoType_ID']) ? (int)$_POST['DemoType_ID'] : null;
}

if (!$lectureId) {
    echo json_encode(false);
    exit;
}

$sql = "INSERT INTO sections (Name, Notes, Lecture_ID, Order_Num, DemoType_ID) 
        VALUES (:Name, :Notes, :Lecture_ID, :Order_Num, :DemoType_ID)";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);
$stmt->bindParam(':Lecture_ID', $lectureId, PDO::PARAM_INT);
$stmt->bindParam(':Order_Num', $orderNum, PDO::PARAM_INT);
$stmt->bindParam(':DemoType_ID', $demoTypeId, $demoTypeId ? PDO::PARAM_INT : PDO::PARAM_NULL);

if ($stmt->execute()) {
    $newId = (int)$pdo->lastInsertId();
    echo json_encode([
        'Section_ID' => $newId,
        'Name' => $name,
        'Notes' => $notes,
        'Lecture_ID' => $lectureId,
        'Order_Num' => $orderNum,
        'DemoType_ID' => $demoTypeId
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(false);
}
?>
