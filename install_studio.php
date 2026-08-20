<?php
// install_studio.php
header('Content-Type: text/html; charset=utf-8');

$projectFiles = [
    // --- 1. ИСПРАВЛЕННЫЙ PPT_Loader.html (Импорт w2ui как модуля) ---
    'PPT_Loader.html' => '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LEMMA - PPT_Loader (Загрузчик)</title>
    <link rel="stylesheet" type="text/css" href="w2ui-2.0.min.css" />
    <style>
        body { font-family: sans-serif; background-color: #f5f6f8; margin: 0; padding: 20px; display: flex; flex-direction: column; align-items: center; }
        .container { background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 500px; margin-top: 40px; }
        .header-area { display: flex; justify-content: space-between; width: 500px; align-items: center; margin-top: 20px; }
        h2 { margin: 0; color: #333; font-weight: 400; }
        .w2ui-form { box-shadow: none !important; background: transparent !important; }
        .back-btn { text-decoration: none; padding: 6px 12px; background: #eee; color: #333; border-radius: 4px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header-area">
        <h2>❶ Утилита PPT_Loader</h2>
        <a href="index.php" class="back-btn">Назад</a>
    </div>
    <div class="container">
        <div id="loader-form-box" style="width: 100%; height: 220px;"></div>
    </div>
    <script type="module">
        // Импортируем w2ui и ваши функции как модули
        import { w2form, w2utils, w2alert } from \'./w2ui-2.0.es6.js\';
        import * as dbLib from \'./dbLib.js\';

        let form = new w2form({
            name: \'loader_form\',
            box: \'#loader-form-box\',
            fields: [
                { field: \'presentation_name\', type: \'text\', required: true, html: { label: \'Название презентации\' } },
                { field: \'create_folder\', type: \'checkbox\', html: { label: \'Создать Фолдер\' } },
                { field: \'folder_input\', type: \'html\', html: {
                    label: \'Папка со слайдами\',
                    html: \'<input id="dir-selector" type="file" webkitdirectory directory multiple style="width: 100%" />\'
                }}
            ],
            record: { presentation_name: \'Новая лекция\', create_folder: true },
            actions: {
                \'Запустить загрузку\': async function () {
                    if (this.validate().length > 0) return;
                    const fileInput = document.getElementById(\'dir-selector\');
                    if (!fileInput || fileInput.files.length === 0) {
                        w2alert(\'Пожалуйста, выберите папку.\');
                        return;
                    }
                    const slideFiles = Array.from(fileInput.files).filter(file => 
                        file.name.toLowerCase().endsWith(\'.jpg\') || file.name.toLowerCase().endsWith(\'.jpeg\')
                    );
                    const totalSlides = slideFiles.length;
                    if (totalSlides === 0) {
                        w2alert(\'В папке не найдено файлов .jpg\');
                        return;
                    }
                    w2utils.lock(this.box, \'Инициализация...\', true);
                    try {
                        let folderId = null;
                        if (this.record.create_folder) {
                            let folderResult = await dbLib.newFolder(this.record.presentation_name);
                            if (folderResult) folderId = folderResult.Folder_ID;
                        }
                        for (let i = 0; i < totalSlides; i++) {
                            const file = slideFiles[i];
                            const slideTitle = file.name.replace(/\\.[^/.]+$/, ""); 
                            w2utils.lock(this.box, `Слайд ${i + 1} из ${totalSlides}`, true);
                            let cardResult = await dbLib.newCard(slideTitle);
                            if (cardResult && cardResult.Card_ID) {
                                const newCardId = cardResult.Card_ID;
                                await dbLib.setCardBlob(newCardId, file);
                                if (folderId) await dbLib.setLinkLabel(newCardId, folderId);
                            }
                        }
                        w2utils.unlock(this.box);
                        w2alert(`Успешно! Слайдов: ${totalSlides}`, \'Успех\');
                    } catch (error) {
                        w2utils.unlock(this.box);
                        w2alert(\'Ошибка при загрузке.\');
                    }
                }
            }
        });
    </script>
</body>
</html>',

    // --- 2. НОВЫЙ ФАЙЛ БЭКЕНДА: dbLib/GetFolders.php (Список папок для сайдбара) ---
    'dbLib/GetFolders.php' => '<?php
require_once \'reconnectDB.php\';
$stmt = $pdo->query("SELECT id, title FROM folders ORDER BY id DESC");
echo json_encode($stmt->fetchAll() ?: [], JSON_UNESCAPED_UNICODE);
exit;',

    // --- 3. НОВЫЙ ФАЙЛ БЭКЕНДА: dbLib/GetFolderCards.php (Слайды конкретной папки) ---
    'dbLib/GetFolderCards.php' => '<?php
require_once \'reconnectDB.php\';
$folderId = (int)$_GET[\'folder_id\'];
$stmt = $pdo->prepare("
    SELECT c.id, c.title, c.notes, c.updated_at 
    FROM cards c
    INNER JOIN card_folder_labels l ON c.id = l.card_id
    WHERE l.folder_id = ?
    ORDER BY c.id ASC
");
$stmt->execute([$folderId]);
$cards = $stmt->fetchAll() ?: [];
foreach ($cards as &$card) {
    $card[\'recid\'] = $card[\'id\']; // для w2ui grid
}
echo json_encode($cards, JSON_UNESCAPED_UNICODE);
exit;',

    // --- 4. НОВЫЙ ФАЙЛ: LEMMA_Studio.html (Рабочая среда) ---
    'LEMMA_Studio.html' => '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LEMMA - Рабочая среда</title>
    <link rel="stylesheet" type="text/css" href="w2ui-2.0.min.css" />
    <style>
        body, html { width: 100%; height: 100%; margin: 0; padding: 0; overflow: hidden; font-family: sans-serif; }
        #studio-layout { width: 100%; height: 100%; }
        .slide-preview { width: 90px; height: 50px; object-fit: cover; border: 1px solid #ccc; border-radius: 4px; }
    </style>
</head>
<body>
    <div id="studio-layout"></div>

    <script type="module">
        import { w2layout, w2sidebar, w2grid } from \'./w2ui-2.0.es6.js\';

        // 1. Создаем каркас приложения
        let layout = new w2layout({
            box: \'#studio-layout\',
            name: \'studio_layout\',
            panels: [
                { type: \'left\', size: 250, resizable: true, minSize: 150 },
                { type: \'main\', html: \'Выберите презентацию слева\' }
            ]
        });

        // 2. Создаем сайдбар для папок
        let sidebar = new w2sidebar({
            name: \'studio_sidebar\',
            nodes: [
                { id: \'root\', text: \'Презентации в базе\', group: true, expanded: true, nodes: [] }
            ],
            async onClick(event) {
                if (event.target === \'root\') return;
                
                // Загружаем слайды выбранной папки
                let response = await fetch(`/dbLib/GetFolderCards.php?folder_id=${event.target}`);
                if (response.ok) {
                    let cards = await response.json();
                    grid.clear();
                    grid.add(cards);
                    grid.refresh();
                }
            }
        });

        // 3. Создаем таблицу для слайдов
        let grid = new w2grid({
            name: \'studio_grid\',
            show: { toolbar: true, footer: true },
            columns: [
                { field: \'recid\', text: \'ID\', size: \'60px\', sortable: true },
                { field: \'preview\', text: \'Превью\', size: \'120px\', style: \'text-align: center\',
                    render: function (record) {
                        // Вызываем наш локальный GetCardBlob.php для показа картинки
                        return `<img src="/dbLib/GetCardBlob.php?id=${record.id}" class="slide-preview" />`;
                    }
                },
                { field: \'title\', text: \'Название слайда\', size: \'40%\', sortable: true },
                { field: \'notes\', text: \'Заметки к слайду\', size: \'60%\' }
            ]
        });

        // Встраиваем компоненты в разметку
        layout.html(\'left\', sidebar);
        layout.html(\'main\', grid);

        // Функция автоматического наполнения сайдбара папками при старте
        async function loadFoldersToSidebar() {
            let response = await fetch(\'/dbLib/GetFolders.php\');
            if (response.ok) {
                let folders = await response.json();
                let nodes = folders.map(f => ({ id: f.id, text: f.title, icon: \'w2ui-icon-folder\' }));
                sidebar.add(\'root\', nodes);
                sidebar.refresh();
            }
        }

        loadFoldersToSidebar();
    </script>
</body>
</html>'
];

// Записываем всё на диск
echo "<h2>Исправление ошибок импорта и развертывание Студии...</h2><ul>";
foreach ($projectFiles as $name => $content) {
    if (file_put_contents(__DIR__ . '/' . $name, $content) !== false) {
        echo "<li style='color: green;'>Файл <b>$name</b> успешно обновлен.</li>";
    } else {
        echo "<li style='color: red;'>Ошибка записи в файл $name.</li>";
    }
}
echo "</ul><p><b>Готово! Все ошибки исправлены, Студия полностью развернута.</b></p>";
