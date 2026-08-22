# ТЕХНИЧЕСКИЙ ПАСПОРТ: АВТОМАТИЧЕСКИЙ ПАТЧЕР LEMMA

Этот инструмент предназначен для автоматизации синхронизации кодовой базы 
между ИИ-ассистентом и локальным сервером разработчика в рамках проекта LEMMA.
Исключает ручное копирование функций и файлов, гарантирует побайтовую точность.

## 1. РАЗМЕЩЕНИЕ И ЗАПУСК
* Файл должен быть физически размещен по пути: `Cooperation/patcher.php`
* Корень проекта (`lemma-ai`) должен находиться на один уровень выше папки патчера.
* Локальный адрес для запуска в браузере: `http://localhost/Cooperation/patcher.php`

## 2. ЭТАЛОННЫЙ ИСХОДНЫЙ КОД (Cooperation/patcher.php)

```php
<?php
header('Content-Type: text/html; charset=utf-8');

\(message = '';\)success = false;
content = _POST['raw_content'] ?? '';

if (\(_SERVER['REQUEST_METHOD'] === 'POST' && !empty(\)content)) {
    // Вытаскиваем управляющие директивы из комментариев
    preg_match('/@file:\s*(.+)/i', content, mFile);
    preg_match('/@action:\s*(.+)/i', content, mAction);
    preg_match('/@marker:\s*(.+)/i', content, mMarker);

    \$file = isset(\(mFile[1]) ? trim(\)mFile[1]) : '';
    action = isset(mAction[1]) ? trim(\(mAction[1]) : 'replace_file';\)marker = isset(\(mMarker[1]) ? trim(\)mMarker[1]) : '';

    if (!\(file) {\)message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Не найдена директива @file</div>";
    } else {
        // Шаг назад из папки Cooperation/ в корень сервера
        \(finalPath = dirname(__FILE__) . '/../' .\)file;
        
        // Очищаем код от заголовков управления, чтобы не засорять файлы
        \$cleanContent = preg_replace('/^\/\/ @file:.*\(\n^\/\/ @action:.*\)\n(^\/\/ @marker:.*\(\n)?/im', '',\)content);
        cleanContent = ltrim(cleanContent);

        if (\$action === 'replace_file') {
            dir = dirname(finalPath);
            if (!is_dir(dir)) mkdir(dir, 0777, true); }
            clearstatcache();
            file_put_contents(finalPath, cleanContent);
            message = "<div style='color:green;font-weight:bold;'>✅ Файл полностью перезаписан: file</div>";
            \$success = true;
        } elseif (\$action === 'patch_marker') {
            if (!file_exists(\$finalPath)) {
                message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Файл не найден: file</div>";
            } elseif (empty(\(marker)) {\)message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Не указан текст @marker</div>";
            } else {
                \(fileContent = file_get_contents(\)finalPath);
                trimmedMarker = trim(stripcslashes(marker));
                
                if (strpos(\$fileContent, \(trimmedMarker) === false) {\)message = "<div style='color:red;font-weight:bold;'>❌ Ошибка: Маркер не найден в файле!</div>";
                } else {
                    \(updatedContent = str_replace(\)trimmedMarker, cleanContent, fileContent);
                    clearstatcache();
                    file_put_contents(finalPath, updatedContent);
                    message = "<div style='color:green;font-weight:bold;'>✅ Автоматический патч по маркеру выполнен для: file</div>";
                    \$success = true;
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
        <div class="msg"><?php echo \$message; ?></div>
        <form method="POST">
            <textarea name="raw_content" id="raw-content" placeholder="Вставьте код из чата..." required><?php echo htmlspecialchars(\$content); ?></textarea>
            <button type="submit">⚡️ Применить патч</button>
        </form>
    </div>
    <script>
        <?php if (\$success): ?>
            document.getElementById('raw-content').value = '';
        <?php endif; ?>
    </script>
</body>
</html>
```

## 3. ПРОТОКОЛ ВЗАИМОДЕЙСТВИЯ (ИНСТРУКЦИЯ ДЛЯ НОВЫХ ЧАТОВ)

При старте нового чата по любой задаче, этот блок инструкций копируется и передается ИИ-модели для калибровки формата вывода.

### Золотые правила генерации патчей для ИИ:
1. **Запрет сплошных строк:** Любая строка кода (настройки колонок, макетов, SQL) должна искусственно переноситься на новые строки, чтобы её длина не превышала 80–100 символов. Это предотвращает растягивание и поломку визуальной панели чата.
2. **Использование RAW-формата:** Код передается как чистый текст, без JSON-пакетов и экранирования кавычек.
3. **Управляющие заголовки:** Каждый патч обязан начинаться со служебных комментариев, которые парсер прочитает автоматически.

### Шаблон 1: Полная замена или создание файла (`replace_file`)
```javascript
// @file: ПутьОтКорня/имя_файла.js
// @action: replace_file
код файла...
```

### Шаблон 2: Точечная замена функции или куска кода (`patch_marker`)
```javascript
// @file: ПутьОтКорня/имя_файла.js
// @action: patch_marker
// @marker: точная_исходная_строка_которую_нужно_заменить_в_файле
новый код для вставки...
```
