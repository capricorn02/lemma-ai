<?php
// Cooperation/patcher.php
header('Content-Type: text/html; charset=utf-8');

$message = '';
$success = false; // Флаг для JS-очистки окна

$file = $_POST['target_file'] ?? '';
$action = $_POST['patch_action'] ?? 'replace_file';
$start_line = intval($_POST['start_line'] ?? 1);
$count_lines = intval($_POST['count_lines'] ?? 0);
$marker = $_POST['marker_text'] ?? '';
$content = $_POST['raw_content'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$file) {
        $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Укажите путь к файлу!</div>";
    } else {
        $finalPath = dirname(__FILE__) . '/../' . trim($file);
        
        // Автоматическая поддержка старого JSON-формата, если пришел он
        $testJson = json_decode(trim($content), true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($testJson)) {
            $action = $testJson['action'] ?? $action;
            $start_line = $testJson['start_line'] ?? $start_line;
            $count_lines = $testJson['count_lines'] ?? $count_lines;
            $marker = $testJson['marker'] ?? $marker;
            $payload = $testJson['content'] ?? '';
            $content = is_array($payload) ? implode("\n", $payload) : $payload;
        }

        if ($action === 'replace_file') {
            $dir = dirname($finalPath);
            if (!is_dir($dir)) { mkdir($dir, 0777, true); }
            clearstatcache();
            file_put_contents($finalPath, $content);
            $message = "<div style='color:green;font-weight:bold;'>✅ Файл полностью перезаписан: $file</div>";
            $success = true;
        } elseif ($action === 'patch_marker') {
            if (!file_exists($finalPath)) {
                $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Файл не найден: $file</div>";
            } elseif (empty($marker)) {
                $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Не указан маркер для поиска!</div>";
            } else {
                $fileContent = file_get_contents($finalPath);
                // Ищем маркер (убираем лишние пробелы по краям для точности)
                $trimmedMarker = trim($marker);
                
                if (strpos($fileContent, $trimmedMarker) === false) {
                    $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Маркер не найден в тексте файла!</div>";
                } else {
                    // Заменяем маркер на новый контент
                    $updatedContent = str_replace($trimmedMarker, $content, $fileContent);
                    clearstatcache();
                    file_put_contents($finalPath, $updatedContent);
                    $message = "<div style='color:green;font-weight:bold;'>✅ Патч по маркеру успешно применен к файлу: $file</div>";
                    $success = true;
                }
            }
        } elseif ($action === 'patch_lines') {
            if (!file_exists($finalPath)) {
                $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Файл не найден: $file</div>";
            } else {
                $lines = file($finalPath, FILE_IGNORE_NEW_LINES);
                $start = $start_line - 1;
                $newLines = explode("\n", str_replace("\r", "", $content));
                array_splice($lines, $start, $count_lines, $newLines);
                clearstatcache();
                file_put_contents($finalPath, implode("\n", $lines));
                $message = "<div style='color:green;font-weight:bold;'>✅ Точечные строки изменены в файле: $file (строка $start_line)</div>";
                $success = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LEMMA — Управление кодовой базой</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f2f5; color: #333; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .row { display: flex; gap: 15px; margin-bottom: 15px; }
        .field { flex: 1; display: flex; flex-direction: column; }
        label { font-weight: bold; margin-bottom: 5px; font-size: 14px; }
        input, select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; }
        textarea { width: 100%; height: 400px; font-family: 'Courier New', monospace; font-size: 14px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical; background: #fdfdfd; }
        button { background: #28a745; color: #fff; border: none; padding: 12px 30px; font-size: 16px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 10px; }
        button:hover { background: #218838; }
        .msg { margin-bottom: 20px; padding: 12px; border-radius: 4px; background: #e2f0d9; border: 1px solid #b7e1cd; }
        .msg:empty { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🛠 Текстовый накатчик кодовой базы LEMMA</h2>
        <div class="msg"><?php echo $message; ?></div>
        <form method="POST" id="patcher-form">
            <div class="row">
                <div class="field" style="flex: 2;">
                    <label>Путь к файлу проекта:</label>
                    <input type="text" name="target_file" value="<?php echo htmlspecialchars($file); ?>" placeholder="DemoObjectTest/app.js" required>
                </div>
                <div class="field">
                    <label>Действие:</label>
                    <select name="patch_action" id="action-select" onchange="togglePatchFields()">
                        <option value="replace_file" <?php if($action == 'replace_file') echo 'selected'; ?>>Полная замена / Создание</option>
                        <option value="patch_marker" <?php if($action == 'patch_marker') echo 'selected'; ?>>Замена по ТЕКСТОВОМУ МАРКЕРУ</option>
                        <option value="patch_lines" <?php if($action == 'patch_lines') echo 'selected'; ?>>Точечная вставка по № строки</option>
                    </select>
                </div>
            </div>
            
            <div class="row" id="marker-field" style="display: <?php echo ($action === 'patch_marker') ? 'flex' : 'none'; ?>;">
                <div class="field">
                    <label style="color: #0056b3;">Текст-маркер (что найти и ЗАМЕНИТЬ):</label>
                    <input type="text" name="marker_text" value="<?php echo htmlspecialchars($marker); ?>" placeholder="Вставьте строку-оригинал из файла...">
                </div>
            </div>
            
            <div class="row" id="patch-fields" style="display: <?php echo ($action === 'patch_lines') ? 'flex' : 'none'; ?>;">
                <div class="field">
                    <label>Начиная со строки №:</label>
                    <input type="number" name="start_line" value="<?php echo $start_line; ?>" min="1">
                </div>
                <div class="field">
                    <label>Сколько строк удалить:</label>
                    <input type="number" name="count_lines" value="<?php echo $count_lines; ?>" min="0">
                </div>
            </div>

            <div class="field" style="margin-top: 15px;">
                <label>Новый исходный код для вставки:</label>
                <textarea name="raw_content" id="raw-content" placeholder="Вставьте сюда код из чата..." required><?php echo htmlspecialchars($content); ?></textarea>
            </div>
            
            <button type="submit">🚀 Запустить обновление файлов</button>
        </form>
    </div>

    <script>
        function togglePatchFields() {
            var action = document.getElementById('action-select').value;
            document.getElementById('marker-field').style.display = (action === 'patch_marker') ? 'flex' : 'none';
            document.getElementById('patch-fields').style.display = (action === 'patch_lines') ? 'flex' : 'none';
        }
        
        // Автоматическая очистка окна с текстом при успешном накате
        <?php if ($success): ?>
            document.getElementById('raw-content').value = '';
        <?php endif; ?>
    </script>
</body>
</html>
