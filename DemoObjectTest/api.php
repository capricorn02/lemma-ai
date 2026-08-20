<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'MySQL-8.2'; 
$user = 'root';
$pass = '';
$dbname = 'demotest';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Ошибка подключения к БД: ' . $e->getMessage()]);
    exit;
}

$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'get_objects':
        try {
            $stmt = $pdo->query("SELECT slide_id, name, demo_type FROM demo_objects ORDER BY slide_id DESC");
            $records = $stmt->fetchAll();
            foreach ($records as &$row) { $row['recid'] = $row['slide_id']; }
            echo json_encode(['status' => 'success', 'records' => $records]);
        } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
        break;

    case 'upload_object':
        if (empty($_FILES['blob_file'])) { echo json_encode(['status' => 'error', 'message' => 'Файл не передан']); exit; }
        $file = $_FILES['blob_file'];
        $demo_type = $_POST['demo_type'] ?? '2D_rastr';
        $custom_name = trim($_POST['name'] ?? '');
        $base_name = ($custom_name !== '') ? $custom_name : pathinfo($file['name'], PATHINFO_FILENAME);
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM demo_objects WHERE name = :name OR name LIKE :name_pattern");
            $stmt->execute(['name' => $base_name, 'name_pattern' => $base_name . ' (%)']);
            $count = $stmt->fetchColumn();
            $final_name = $count > 0 ? $base_name . " ($count)" : $base_name;
            $blob_data = file_get_contents($file['tmp_name']);
            $stmt = $pdo->prepare("INSERT INTO demo_objects (name, demo_type, blob_data) VALUES (?, ?, ?)");
            $stmt->bindParam(1, $final_name);
            $stmt->bindParam(2, $demo_type);
            $stmt->bindParam(3, $blob_data, PDO::PARAM_LOB);
            $stmt->execute();
            echo json_encode(['status' => 'success', 'message' => 'Объект успешно загружен']);
        } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
        break;

    case 'delete_object':
        $slide_id = intval($_POST['slide_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("DELETE FROM demo_objects WHERE slide_id = ?");
            $stmt->execute([$slide_id]);
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
        break;

    case 'save_scenario':
        $slide_id = intval($_POST['slide_id'] ?? 0);
        $custom_name = trim($_POST['name'] ?? '');
        $commands_json = $_POST['commands'] ?? '[]';
        try {
            $stmt = $pdo->prepare("SELECT name, blob_data FROM demo_objects WHERE slide_id = ?");
            $stmt->execute([$slide_id]);
            $origin = $stmt->fetch();
            if (!$origin) { echo json_encode(['status' => 'error', 'message' => 'Исходный объект не найден']); exit; }
            $base_name = ($custom_name !== '') ? $custom_name : $origin['name'] . ' - Запись';
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM demo_commands WHERE name = :name OR name LIKE :name_pattern");
            $stmt->execute(['name' => $base_name, 'name_pattern' => $base_name . ' (%)']);
            $count = $stmt->fetchColumn();
            $final_name = $count > 0 ? $base_name . " ($count)" : $base_name;
            $stmt = $pdo->prepare("INSERT INTO demo_commands (slide_id, name, commands_json, blob_data) VALUES (?, ?, ?, ?)");
            $stmt->bindParam(1, $slide_id);
            $stmt->bindParam(2, $final_name);
            $stmt->bindParam(3, $commands_json);
            $stmt->bindParam(4, $origin['blob_data'], PDO::PARAM_LOB);
            $stmt->execute();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
        break;

    case 'get_scenarios':
        try {
            $stmt = $pdo->query("SELECT section_id, slide_id, name FROM demo_commands ORDER BY section_id DESC");
            $records = $stmt->fetchAll();
            foreach ($records as &$row) { $row['recid'] = $row['section_id']; }
            echo json_encode(['status' => 'success', 'records' => $records]);
        } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
        break;

    case 'get_blob':
        $type = $_GET['source'] ?? 'object';
        $id = intval($_GET['id'] ?? 0);
        try {
            if ($type === 'command') {
                $stmt = $pdo->prepare("SELECT blob_data FROM demo_commands WHERE section_id = ?");
            } else {
                $stmt = $pdo->prepare("SELECT blob_data FROM demo_objects WHERE slide_id = ?");
            }
            $stmt->execute([$id]);
            $stmt->bindColumn(1, $lob, PDO::PARAM_LOB);
            if ($stmt->fetch(PDO::FETCH_BOUND)) {
                header('Content-Type: application/octet-stream');
                fpassthru($lob);
                exit;
            } else { header("HTTP/1.0 404 Not Found"); }
        } catch (Exception $e) { header("HTTP/1.0 500 Server Error"); }
        break;

    case 'get_scenario_data':
        $section_id = intval($_GET['section_id'] ?? 0);
        try {
            $stmt = $pdo->prepare("SELECT section_id, name, commands_json, demo_objects.demo_type FROM demo_commands LEFT JOIN demo_objects USING (slide_id) WHERE section_id = ?");
            $stmt->execute([$section_id]);
            $data = $stmt->fetch();
            echo json_encode($data ? ['status' => 'success', 'data' => $data] : ['status' => 'error', 'message' => 'Не найден']);
        } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
        break;
}
