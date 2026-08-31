<?php
// dbLayer/dbMML/setSection.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/reconnectDB_MML.php';

if (isset($_POST['Section'])) {
    $section = is_string($_POST['Section']) ? json_decode($_POST['Section'], true) : $_POST['Section'];
    $sectionId = $section['Section_ID'] ?? $section['ID'] ?? null;
    $name = $section['Name'] ?? $section['Title'] ?? '';
    $notes = $section['Notes'] ?? '';
    $orderNum = isset($section['Order_Num']) ? (int)$section['Order_Num'] : (isset($section['Order_NUM']) ? (int)$section['Order_NUM'] : null);
    $demoTypeId = !empty($section['DemoType_ID']) ? (int)$section['DemoType_ID'] : null;
} else {
    $sectionId = $_POST['Section_ID'] ?? $_POST['ID'] ?? null;
    $name = $_POST['Name'] ?? $_POST['Title'] ?? '';
    $notes = $_POST['Notes'] ?? '';
    $orderNum = isset($_POST['Order_Num']) ? (int)$_POST['Order_Num'] : (isset($_POST['Order_NUM']) ? (int)$_POST['Order_NUM'] : null);
    $demoTypeId = !empty($_POST['DemoType_ID']) ? (int)$_POST['DemoType_ID'] : null;
}

if (!$sectionId) {
    echo json_encode(false);
    exit;
}

$sql = "UPDATE sections SET Name = :Name, Notes = :Notes";
if ($orderNum !== null) {
    $sql .= ", Order_Num = :Order_Num";
}
if ($demoTypeId !== null) {
    $sql .= ", DemoType_ID = :DemoType_ID";
}
$sql .= " WHERE Section_ID = :Section_ID";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':Name', $name);
$stmt->bindParam(':Notes', $notes);
if ($orderNum !== null) {
    $stmt->bindParam(':Order_Num', $orderNum, PDO::PARAM_INT);
}
if ($demoTypeId !== null) {
    $stmt->bindParam(':DemoType_ID', $demoTypeId, PDO::PARAM_INT);
}
$stmt->bindParam(':Section_ID', $sectionId, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo json_encode(true);
} else {
    echo json_encode(false);
}
?>
