<?php
// Cooperation/patcher.php
header('Content-Type: text/html; charset=utf-8');

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['patch_json'])) {
    $patchData = json_decode(trim($_POST['patch_json']), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Неверный формат JSON!</div>";
    } else {
        $file = $patchData['file'] ?? '';
        $action = $patchData['action'] ?? ''; // 'replace_file', 'patch_lines'
        
        if (!$file) {
            $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Не указан целевой файл!</div>";
        } else {
            // Раз корень хоста ai/ — это и есть корень репозитория:
            $finalPath = realpath(dirname(__FILE__) . '/../' . $file);
            
            if ($finalPath === false && $action !== 'replace_file') {
                $finalPath = dirname(__FILE__) . '/../' . $file;
            }

            if ($action === 'replace_file') {
                $dir = dirname($finalPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                clearstatcache();
                file_put_contents($finalPath, $patchData['content']);
                $message = "<div style='color:green;font-weight:bold;'>✅ Файл полностью заменен: $file</div>";
            } elseif ($action === 'patch_lines') {
                if (!file_exists($finalPath)) {
                    $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Файл не найден: $file</div>";
                } else {
                    $lines = file($finalPath, FILE_IGNORE_NEW_LINES);
                    $start = intval($patchData['start_line'] ?? 0) - 1;
                    $count = intval($patchData['count_lines'] ?? 0);
                    $newContent = $patchData['content'] ?? '';
                    
                    if ($start < 0 || $start > count($lines)) {
                        $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Неверный номер строки!</div>";
                    } else {
                        $newLines = explode("\n", str_replace("\r", "", $newContent));
                        array_splice($lines, $start, $count, $newLines);
                        clearstatcache();
                        file_put_contents($finalPath, implode("\n", $lines));
                        $message = "<div style='color:green;font-weight:bold;'>✅ Точечный патч применен: $file (строка $patchData[start_line])</div>";
                    }
                }
            } else {
                $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Неизвестное действие!</div>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LEMMA — Глобальный накатчик патчей</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f2f5; color: #333; }
        .container { max-width: 850px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        textarea { width: 100%; height: 350px; font-family: monospace; font-size: 14px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #28a745; color: #fff; border: none; padding: 12px 25px; font-size: 16px; border-radius: 4px; cursor: pointer; margin-top: 10px; font-weight: bold; }
        button:hover { background: #218838; }
        .msg { margin-bottom: 20px; padding: 10px; border-radius: 4px; background: #e2f0d9; border: 1px solid #b7e1cd; }
        .msg:empty { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🛠 Глобальный инсталлятор патчей LEMMA</h2>
        <div class="msg"><?php echo $message; ?></div>
        <form method="POST">
            <label style="display:block; margin-bottom:5px; font-weight:bold;">Вставьте JSON-инструкцию патча:</label>
            <textarea name="patch_json" placeholder='{"file": "DemoObjectTest/app.js", "action": "patch_lines", ...}' required></textarea>
            <button type="submit">🚀 Запустить накат изменений</button>
        </form>
    </div>
</body>
</html>
