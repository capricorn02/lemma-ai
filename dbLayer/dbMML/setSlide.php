<?php
// dbLayer/dbMML/setSlide.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

if (isset($_POST['Slide'])) {
    $slide = is_string($_POST['Slide']) ? json_decode($_POST['Slide'], true) : $_POST['Slide'];
    $slideId = $slide['Slide_ID'] ?? $slide['ID'] ?? null;
    $name = $slide['Name'] ?? $slide['Title'] ?? '';
    $notes = $slide['Notes'] ?? '';
    $orderNum = isset($slide['Order_Num']) ? (int)$slide['Order_Num'] : (isset($slide['Order_NUM']) ? (int)$slide['Order_NUM'] : null);
    $demoTypeId = !empty($slide['DemoType_ID']) ? (int)$slide['DemoType_ID'] : null;
} else {
    $slideId = $_POST['Slide_ID'] ?? $_POST['ID'] ?? null;
    $name = $_POST['Name'] ?? $_POST['Title'] ?? '';
    $notes = $_POST['Notes'] ?? '';
    $orderNum = isset($_POST['Order_Num']) ? (int)$_POST['Order_Num'] : (isset($_POST['Order_NUM']) ? (int)$_POST['Order_NUM'] : null);
    $demoTypeId = !empty($_POST['DemoType_ID']) ? (int)$_POST['DemoType_ID'] : null;
}

if (!$slideId) {
    echo json_encode(false);
    exit;
}

$sql = "UPDATE slides SET Name = :Name, Notes = :Notes";
if ($orderNum !== null) {
    $sql .= ", Order_Num = :Order_Num";
}
if ($demoTypeId !== null) {
    $sql .= ", DemoType_ID = :DemoType_ID";
}
$sql .= " WHERE Slide_ID = :Slide_ID";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);
if ($orderNum !== null) {
    $stmt->bindParam(':Order_Num', $orderNum, PDO::PARAM_INT);
}
if ($demoTypeId !== null) {
    $stmt->bindParam(':DemoType_ID', $demoTypeId, PDO::PARAM_INT);
}
$stmt->bindParam(':Slide_ID', $slideId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
