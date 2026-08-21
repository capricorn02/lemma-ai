import { w2ui, w2layout, w2popup, w2alert } from '/w2ui-2.0.es6.js';
import SlideRecord2D from './js/slideRastr2d.js';
import SlideRecordVideo from './js/slideVideo.js';
import { initGridObjects } from './js/appGridObjects.js';
import { initGridScenarios } from './js/appGridScenarios.js';

const AppState = { activeSlideInstance: null, currentSlideId: null, currentDemoType: null, timerInterval: null, startTime: 0 };

document.addEventListener('DOMContentLoaded', function() {
    new w2layout({
        box: '#main-layout', name: 'mainLayout',
        panels: [
            { type: 'top', size: 40, content: '<h3 style=\"margin:8px 15px; color:#333;\">LEMMA: Песочница ЖД</h3>' },
            { type: 'left', size: '50%', resizable: true, title: 'Исходные заготовки объектов' },
            { type: 'main', size: '50%', title: 'Записанные ЖД демонстрации' }
        ]
    });

    initGridObjects(openStudioWindow, openUploadDialog);
    initGridScenarios(openPlayerWindow);
});

function openUploadDialog() {
    w2popup.open({
        title: 'Загрузка объекта в БД', body: document.getElementById('upload-form-box').innerHTML, width: 450, height: 280,
        buttons: '<button class=\"w2ui-btn w2ui-btn-blue\" id=\"popup-btn-upload\">Сохранить</button>',
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
                            const playerArgs = [blob, commandsArray];
                            if (scenario.demo_type === 'video') {
                                AppState.activeSlideInstance = new SlideRecordVideo(...playerArgs);
                            } else {
                                AppState.activeSlideInstance = new SlideRecord2D(...playerArgs);
                            }
                            AppState.activeSlideInstance.render(wp);
                            initPlayerEvents();
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
