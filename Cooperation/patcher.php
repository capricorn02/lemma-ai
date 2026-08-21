<?php
// Cooperation/patcher.php
header('Content-Type: text/html; charset=utf-8');

$message = '';
$success = false;
$content = $_POST['raw_content'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($content)) {
    // Регулярными выражениями вытаскиваем директивы из комментариев кода
    preg_match('/@file:\s*(.+)/i', $content, $mFile);
    preg_match('/@action:\s*(.+)/i', $content, $mAction);
    preg_match('/@marker:\s*(.+)/i', $content, $mMarker);

    $file = isset($mFile[1]) ? trim($mFile[1]) : '';
    $action = isset($mAction[1]) ? trim($mAction[1]) : 'replace_file';
    $marker = isset($mMarker[1]) ? trim($mMarker[1]) : '';

    if (!$file) {
        $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: В коде не найдена директива // @file: путь_к_файлу</div>";
    } else {
        $finalPath = dirname(__FILE__) . '/../' . $file;
        
        // Удаляем блок системных комментариев из итогового кода, чтобы не засорять файлы проекта
        $cleanContent = preg_replace('/^\/\/ @file:.*$\n^\/\/ @action:.*$\n(^\/\/ @marker:.*$\n)?/im', '', $content);
        $cleanContent = ltrim($cleanContent);

        if ($action === 'replace_file') {
            $dir = dirname($finalPath);
            if (!is_dir($dir)) { mkdir($dir, 0777, true); }
            clearstatcache();
            file_put_contents($finalPath, $cleanContent);
            $message = "<div style='color:green;font-weight:bold;'>✅ Файл полностью перезаписан: $file</div>";
            $success = true;
        } elseif ($action === 'patch_marker') {
            if (!file_exists($finalPath)) {
                $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Файл не найден: $file</div>";
            } elseif (empty($marker)) {
                $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Не указан текст в директиве // @marker: ...</div>";
            } else {
                $fileContent = file_get_contents($finalPath);
                // Декодируем маркер, если в нем были экранированы символы
                $trimmedMarker = trim(stripcslashes($marker));
                
                if (strpos($fileContent, $trimmedMarker) === false) {
                    $message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Маркер не найден в файле! Проверьте репозиторий.</div>";
                } else {
                    $updatedContent = str_replace($trimmedMarker, $cleanContent, $fileContent);
                    clearstatcache();
                    file_put_contents($finalPath, $updatedContent);
                    $message = "<div style='color:green;font-weight:bold;'>✅ Автоматический патч по маркеру выполнен для: $file</div>";
                    $success = true;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LEMMA — Автоматический интеллектуальный патчер</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f0f2f5; color: #333; }
        .container { max-width: 900px; margin: 0 auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        textarea { width: 100%; height: 450px; font-family: 'Courier New', monospace; font-size: 14px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background: #fdfdfd; }
        button { background: #007bff; color: #fff; border: none; padding: 12px 30px; font-size: 16px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 15px; }
        button:hover { background: #0056b3; }
        .msg { margin-bottom: 20px; padding: 12px; border-radius: 4px; background: #e2f0d9; border: 1px solid #b7e1cd; }
        .msg:empty { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🚀 Автоматический накатчик патчей LEMMA</h2>
        <div class="msg"><?php echo $message; ?></div>
        <form method="POST">
            <label style="display:block; margin-bottom:10px; font-weight:bold; font-size:15px;">Просто вставьте весь блок кода из чата (вместе с системными комментариями):</label>
            <textarea name="raw_content" id="raw-content" placeholder="Вставьте код сюда..." required><?php echo htmlspecialchars($content); ?></textarea>
            <button type="submit">⚡️ Распознать и применить патч</button>
        </form>
    </div>
    <script>
        <?php if ($success): ?>
            document.getElementById('raw-content').value = '';
        <?php endif; ?>
    </script>
</body>
</html>
