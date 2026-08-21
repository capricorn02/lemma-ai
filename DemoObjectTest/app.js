import { query, w2ui, w2layout, w2grid, w2popup, w2confirm, w2alert, w2prompt } from '/w2ui-2.0.es6.js';
import SlideRecord2D from './js/slideRastr2d.js';
import SlideRecordVideo from './js/slideVideo.js';

const AppState = { activeSlideInstance: null, currentSlideId: null, currentDemoType: null, timerInterval: null, startTime: 0 };

document.addEventListener('DOMContentLoaded', function() {
    new w2layout({
        box: '#main-layout', name: 'mainLayout',
        panels: [
            { type: 'top', size: 40, content: '<h3 style="margin:8px 15px; color:#333;">LEMMA: Песочница ЖД</h3>' },
            { type: 'left', size: '50%', resizable: true, title: 'Исходные заготовки объектов' },
            { type: 'main', size: '50%', title: 'Записанные ЖД демонстрации' }
        ]
    });

    new w2grid({
        box: w2ui.mainLayout.el('left'), name: 'gridObjects', url: 'api.php?action=get_objects', method: 'GET',
        show: { toolbar: true, footer: true, toolbarAdd: true, toolbarDelete: true },
        columns: [
            { field: 'slide_id', text: 'slide_id', size: '80px', sortable: true },
            { field: 'name', text: 'Название объекта', size: '100%', sortable: true },
            { field: 'demo_type', text: 'Тип демо', size: '120px', sortable: true }
        ],
        toolbar: {
            items: [{ type: 'button', id: 'btn-record', text: '🎙 Записать ЖД', icon: 'w2ui-icon-pencil', disabled: true }],
            onClick(ev) {
                if (ev.target === 'btn-record') {
                    const sel = w2ui.gridObjects.getSelection();
                    if (sel.length > 0) { const sId = sel; openStudioWindow(w2ui.gridObjects.get(sId)); }
                }
            }
        },
        onSelect() { setTimeout(() => w2ui.gridObjects.toolbar.enable('btn-record'), 10); },
        onUnselect() { setTimeout(() => { if (w2ui.gridObjects.getSelection().length === 0) w2ui.gridObjects.toolbar.disable('btn-record'); }, 10); },
        onAdd() { openUploadDialog(); },
        onDelete(ev) {
            ev.preventDefault(); const sel = w2ui.gridObjects.getSelection(); if (sel.length === 0) return;
            w2confirm('Удалить объект?').yes(() => {
                query('api.php', { action: 'delete_object', slide_id: sel }).then(() => { w2ui.gridObjects.reload(); w2ui.gridObjects.toolbar.disable('btn-record'); });
            });
        }
    });

    new w2grid({
        box: w2ui.mainLayout.el('main'), name: 'gridScenarios', url: 'api.php?action=get_scenarios', method: 'GET',
        show: { toolbar: true, footer: true },
        columns: [
            { field: 'section_id', text: 'section_id', size: '90px', sortable: true },
            { field: 'slide_id', text: 'slide_id', size: '80px', sortable: true },
            { field: 'name', text: 'Название сценария ЖД', size: '100%', sortable: true }
        ],
        toolbar: {
            items: [{ type: 'button', id: 'btn-play', text: '▶ Просмотр ЖД', icon: 'w2ui-icon-search', disabled: true }],
            onClick(ev) { if (ev.target === 'btn-play') { const sel = w2ui.gridScenarios.getSelection(); if (sel.length > 0) openPlayerWindow(w2ui.gridScenarios.get(sel).section_id); } }
        },
        onSelect() { setTimeout(() => w2ui.gridScenarios.toolbar.enable('btn-play'), 10); },
        onUnselect() { setTimeout(() => { if (w2ui.gridScenarios.getSelection().length === 0) w2ui.gridScenarios.toolbar.disable('btn-play'); }, 10); }
    });
});

function openUploadDialog() {
    w2popup.open({
        title: 'Загрузка объекта в БД', body: document.getElementById('upload-form-box').innerHTML, width: 450, height: 280,
        buttons: '<button class="w2ui-btn w2ui-btn-blue" id="popup-btn-upload">Сохранить</button>',
        onOpen(ev) {
            ev.done(() => {
                document.getElementById('popup-btn-upload').onclick = () => {
                    const fd = new FormData(document.querySelector('#w2ui-popup #upload-file-form'));
                    fd.append('action', 'upload_object');
                    fetch('api.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => { if (res.status === 'success') { w2popup.close(); w2ui.gridObjects.reload(); } else { w2alert(res.message); } });
                };
            });
        }
    });
}

function openStudioWindow(gridRow) {
    if(!gridRow) return;
    AppState.currentSlideId = gridRow.slide_id; AppState.currentDemoType = gridRow.demo_type;
    w2popup.open({
        title: 'Студия записи LEMMA — Объект: ' + gridRow.name, body: document.getElementById('studio-window-box').innerHTML, width: 900, height: 650, modal: true,
        onOpen(ev) {
            ev.done(() => {
                setTimeout(() => {
                    const wp = document.querySelector('#w2ui-popup #studio-workplace');
                    const tp = document.querySelector('#w2ui-popup #studio-custom-tools');
                    wp.style.width = '800px'; wp.style.height = '600px'; wp.style.margin = '0 auto';
                    fetch('api.php?action=get_blob&source=object&id=' + AppState.currentSlideId).then(r => r.blob()).then(blob => {
                        AppState.activeSlideInstance = AppState.currentDemoType === 'video' ? new SlideRecordVideo([blob]) : new SlideRecord2D([blob]);
                        AppState.activeSlideInstance.render(wp); AppState.activeSlideInstance.renderTools(tp); initStudioEvents();
                    });
                }, 150);
            });
        },
        onClose() { if (AppState.activeSlideInstance) AppState.activeSlideInstance.finish(); clearInterval(AppState.timerInterval); AppState.activeSlideInstance = null; }
    });
}

function initStudioEvents() {
    const btnStart = document.querySelector('#w2ui-popup #btn-start-record');
    const btnStop = document.querySelector('#w2ui-popup #btn-stop-record');
    const timerDisplay = document.querySelector('#w2ui-popup #record-timer');
    const statusTxt = document.querySelector('#w2ui-popup #studio-status');
    
    btnStart.onclick = () => {
        btnStart.disabled = true; btnStop.disabled = false; statusTxt.textContent = '🔴 Запись движений...';
        AppState.startTime = Date.now();
        AppState.timerInterval = setInterval(() => {
            const diff = Date.now() - AppState.startTime;
            const ms = String(diff % 1000).padStart(3, '0').slice(0, 2);
            const secs = String(Math.floor(diff / 1000) % 60).padStart(2, '0');
            const mins = String(Math.floor(diff / 60000)).padStart(2, '0');
            timerDisplay.textContent = mins + ':' + secs + '.' + ms;
        }, 30);
        AppState.activeSlideInstance.start();
    };
    
    btnStop.onclick = () => {
        clearInterval(AppState.timerInterval); btnStop.disabled = true;
        AppState.activeSlideInstance.finish(); const recordedCommands = AppState.activeSlideInstance.getCommands();
        const val = prompt('Введите название сценария демонстрации:', '');
        if (!val) { w2popup.close(); return; }
        const fd = new FormData(); fd.append('action', 'save_scenario'); fd.append('slide_id', AppState.currentSlideId); fd.append('name', val); fd.append('commands', JSON.stringify(recordedCommands));
        fetch('api.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => { if (res.status === 'success') { w2popup.close(); w2ui.gridScenarios.reload(); } else { w2alert(res.message); } });
    };
}

function openPlayerWindow(sectionId) {
    w2popup.open({
        title: 'Воспроизведение ЖД', body: document.getElementById('player-window-box').innerHTML, width: 900, height: 650, modal: true,
        onOpen(ev) {
            ev.done(() => {
                setTimeout(() => {
                    const wp = document.querySelector('#w2ui-popup #player-workplace');
                    const td = document.querySelector('#w2ui-popup #player-title');
                    wp.style.width = '800px'; wp.style.height = '600px'; wp.style.margin = '0 auto';
                    fetch('api.php?action=get_scenario_data&section_id=' + sectionId).then(r => r.json()).then(res => {
                        if (res.status !== 'success') { w2alert(res.message); w2popup.close(); return; }
                        const scenario = res.data; td.textContent = 'Сценарий: ' + scenario.name;
                        const commandsArray = JSON.parse(scenario.commands_json);
                        fetch('api.php?action=get_blob&source=command&id=' + sectionId).then(r => r.blob()).then(blob => {
                            AppState.activeSlideInstance = scenario.demo_type === 'video' ? new SlideRecordVideo(blob, commandsArray) : new SlideRecord2D(blob, commandsArray);
                            AppState.activeSlideInstance.render(wp); initPlayerEvents();
                        });
                    });
                }, 150);
            });
        },
        onClose() { if (AppState.activeSlideInstance) AppState.activeSlideInstance.pause(); AppState.activeSlideInstance = null; }
    });
}

function initPlayerEvents() { 
    const btnPlay = document.querySelector('#w2ui-popup #btn-player-play');
    const btnPause = document.querySelector('#w2ui-popup #btn-player-pause');
    const btnClose = document.querySelector('#w2ui-popup #btn-player-close');
    btnPlay.onclick = () => AppState.activeSlideInstance.play();
    btnPause.onclick = () => AppState.activeSlideInstance.pause();
    btnClose.onclick = () => w2popup.close();
}