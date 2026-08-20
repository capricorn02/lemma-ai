<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>LEMMA - Тестовый стенд Живых Демонстраций</title>
    <link rel="stylesheet" type="text/css" href="/w2ui-2.0.min.css" />
    <style>
        body, html {
            margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden;
            font-family: Arial, sans-serif; background-color: #f4f4f6;
        }
        #main-layout { width: 100%; height: 100%; }
        .slide-container-box {
            width: 100%; height: 100%; background: #222;
            display: flex; align-items: center; justify-content: center; position: relative;
        }
        .studio-controls {
            padding: 10px; background: #eee; border-bottom: 1px solid #ccc;
            display: flex; gap: 10px; align-items: center;
        }
        .timer-display {
            font-size: 18px; font-weight: bold; font-family: monospace;
            background: #333; color: #0f0; padding: 4px 10px; border-radius: 4px;
        }
    </style>
</head>
<body>
    <div id="main-layout"></div>

    <div id="upload-form-box" style="display:none; padding: 15px;">
        <form id="upload-file-form" enctype="multipart/form-data">
            <div style="margin-bottom:10px;">
                <label style="display:block; margin-bottom:5px;">Название объекта (необязательно):</label>
                <input type="text" id="obj_name" name="name" style="width:100%; padding:5px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:10px;">
                <label style="display:block; margin-bottom:5px;">Тип объекта:</label>
                <select id="obj_type" name="demo_type" style="width:100%; padding:5px;">
                    <option value="2D_rastr">2D Растр (Картинка с рисованием)</option>
                    <option value="video">Видео-слайд</option>
                </select>
            </div>
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:5px;">Выбрать файл:</label>
                <input type="file" id="obj_file" name="blob_file" required style="width:100%;">
            </div>
        </form>
    </div>

    <div id="studio-window-box" style="display:none; width:100%; height:100%; flex-direction:column;">
        <div class="studio-controls">
            <button id="btn-start-record" class="w2ui-btn w2ui-btn-blue">🔴 Старт записи</button>
            <button id="btn-stop-record" class="w2ui-btn w2ui-btn-red" disabled>⏹ Стоп записи</button>
            <div id="record-timer" class="timer-display">00:00.00</div>
            <div id="studio-custom-tools" style="display:flex; gap:5px; border-left:1px solid #ccc; padding-left:10px;"></div>
            <span id="studio-status" style="margin-left:auto; color:#666;">Готов к записи</span>
        </div>
        <div style="flex:1; position:relative; background:#111;">
            <div id="studio-workplace" class="slide-container-box"></div>
        </div>
    </div>

    <div id="player-window-box" style="display:none; width:100%; height:100%; flex-direction:column;">
        <div class="studio-controls">
            <button id="btn-player-play" class="w2ui-btn w2ui-btn-green">▶ Воспроизвести</button>
            <button id="btn-player-pause" class="w2ui-btn">⏸ Пауза</button>
            <button id="btn-player-close" class="w2ui-btn w2ui-btn-red">Выход</button>
            <span id="player-title" style="margin-left:15px; font-weight:bold;"></span>
        </div>
        <div style="flex:1; position:relative; background:#111;">
            <div id="player-workplace" class="slide-container-box"></div>
        </div>
    </div>

    <script type="module" src="app.js"></script>
</body>
</html>