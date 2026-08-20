<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LEMMA - Конструктор Презентаций</title>
    <!-- Подключаем ЛОКАЛЬНЫЕ стили w2ui -->
    <link rel="stylesheet" type="text/css" href="w2ui-2.0.min.css" />
    <style>
        body { font-family: sans-serif; background-color: #f5f6f8; margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .dashboard { background: #ffffff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; width: 450px; }
        h1 { color: #333; margin-bottom: 30px; font-weight: 400; }
        .menu-btn { display: block; width: 100%; padding: 15px; margin: 15px 0; font-size: 16px; text-align: center; box-sizing: border-box; text-decoration: none; background: #eee; color: #333; border-radius: 4px; }
        .menu-btn:hover { background: #ddd; }
        .danger-zone { margin-top: 40px; border-top: 1px solid #eee; padding-top: 20px; }
        .btn-red { background: #f44336; color: white; border: none; cursor: pointer; }
        .btn-red:hover { background: #d32f2f; }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>LEMMA Презентации</h1>
        <a href="PPT_Loader.html" class="menu-btn">❶ PPT_Loader (Загрузчик)</a>
        <a href="LEMMA_Studio.html" class="menu-btn">❷ LEMMA_Studio (Рабочая среда)</a>
        <div class="danger-zone">
            <button onclick="recreateDatabase()" class="menu-btn btn-red">⚙ Пересоздать схему БД (RecreateDB)</button>
        </div>
    </div>
    <script type="module">
        import * as dbLib from './dbLib.js';
        window.onload = async () => {
            await dbLib.connectDB();
        };
        window.recreateDatabase = async () => {
            if (confirm("ВНИМАНИЕ! Это действие ПОЛНОСТЬЮ УДАЛИТ базу данных и все слайды. Вы уверены?")) {
                let response = await fetch('/dbLib/RecreateDB.php');
                if (response.ok) {
                    let result = await response.json();
                    if (result.status === true) {
                        alert(result.message);
                        await dbLib.connectDB();
                    } else { alert("Ошибка: " + result.error); }
                } else { alert("Не удалось связаться с сервером."); }
            }
        };
    </script>
</body>
</html>