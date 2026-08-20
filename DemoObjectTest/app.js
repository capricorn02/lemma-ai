import { query, w2ui, w2layout, w2grid, w2popup, w2confirm, w2alert, w2prompt } from "/w2ui-2.0.es6.js";
import SlideRecord2D from "./js/slideRastr2d.js";
import SlideRecordVideo from "./js/slideVideo.js";

const AppState = { activeSlideInstance: null, currentSlideId: null, currentDemoType: null, timerInterval: null, startTime: 0 };

document.addEventListener("DOMContentLoaded", function() {
    new w2layout({
        box: "#main-layout",
        name: "mainLayout",
        panels: [
            { type: "top", size: 40, content: "<h3 style=\"margin:8px 15px; color:#333;\">LEMMA: Отладочная песочница «Живых Демонстраций»</h3>" },
            { type: "left", size: "50%", resizable: true, title: "Исходные заготовки объектов (demo_objects)" },
            { type: "main", size: "50%", title: "Записанные ЖД демонстрации (demo_commands)" }
        ]
    });

    new w2grid({
        box: w2ui.mainLayout.el("left"),
        name: "gridObjects",
        url: "api.php?action=get_objects",
        method: "GET",
        show: { toolbar: true, footer: true, toolbarAdd: true, toolbarDelete: true },
        columns: [
            { field: "slide_id", text: "slide_id", size: "80px", sortable: true },
            { field: "name", text: "Название объекта", size: "100%", sortable: true },
            { field: "demo_type", text: "Тип демо", size: "120px", sortable: true }
        ],
        toolbar: {
            items: [{ type: "button", id: "btn-record", text: "🎙 Записать демонстрацию", icon: "w2ui-icon-pencil", disabled: true }],
            onClick(event) {
                if (event.target === "btn-record") {
                    const selected = w2ui.gridObjects.getSelection();
                    if (selected.length > 0) { openStudioWindow(w2ui.gridObjects.get(selected[0])); }
                }
            }
        },
        onSelect(event) { event.done(function() { w2ui.gridObjects.toolbar.enable("btn-record"); }); },
        onUnselect(event) { event.done(function() { if (w2ui.gridObjects.getSelection().length === 0) { w2ui.gridObjects.toolbar.disable("btn-record"); } }); },
        onAdd(event) { openUploadDialog(); },
        onDelete(event) {
            event.preventDefault(); const selected = w2ui.gridObjects.getSelection(); if (selected.length === 0) return;
            w2confirm("Удалить выбранный объект?").yes(function() {
                query("api.php", { action: "delete_object", slide_id: selected[0] }).then(function() { w2ui.gridObjects.reload(); w2ui.gridObjects.toolbar.disable("btn-record"); });
            });
        }
    });

    new w2grid({
        box: w2ui.mainLayout.el("main"),
        name: "gridScenarios",
        url: "api.php?action=get_scenarios",
        method: "GET",
        show: { toolbar: true, footer: true },
        columns: [
            { field: "section_id", text: "section_id", size: "90px", sortable: true },
            { field: "slide_id", text: "slide_id", size: "80px", sortable: true },
            { field: "name", text: "Название сценария ЖД", size: "100%", sortable: true }
        ],
        toolbar: {
            items: [{ type: "button", id: "btn-play", text: "▶ Просмотр ЖД (Студент)", icon: "w2ui-icon-search", disabled: true }],
            onClick(event) {
                if (event.target === "btn-play") {
                    const selected = w2ui.gridScenarios.getSelection();
                    if (selected.length > 0) { openPlayerWindow(w2ui.gridScenarios.get(selected[0]).section_id); }
                }
            }
        },
        onSelect(event) { event.done(function() { w2ui.gridScenarios.toolbar.enable("btn-play"); }); },
        onUnselect(event) { event.done(function() { if (w2ui.gridScenarios.getSelection().length === 0) { w2ui.gridScenarios.toolbar.disable("btn-play"); } }); }
    });
});

function openUploadDialog() {
    w2popup.open({
        title: "Загрузка нового демообъекта в БД",
        body: document.getElementById("upload-form-box").innerHTML,
        width: 450, height: 280,
        buttons: "<button class=\"w2ui-btn w2ui-btn-blue\" id=\"popup-btn-upload\">Сохранить в БД</button> <button class=\"w2ui-btn\" onclick=\"w2popup.close()\">Отмена</button>",
        onOpen(event) {
            event.done(function() {
                document.getElementById("popup-btn-upload").onclick = function() {
                    const formEl = document.querySelector("#w2ui-popup #upload-file-form");
                    const fileInput = formEl.querySelector("#obj_file");
                    if (!fileInput.files || fileInput.files.length === 0) { w2alert("Пожалуйста, выберите файл подложки!"); return; }
                    const formData = new FormData(formEl); formData.append("action", "upload_object");
                    fetch("api.php", { method: "POST", body: formData }).then(r => r.json()).then(res => {
                        if (res.status === "success") { w2popup.close(); w2ui.gridObjects.reload(); } else { w2alert(res.message); }
                    });
                };
            });
        }
    });
}

function openStudioWindow(gridRow) {
    AppState.currentSlideId = gridRow.slide_id;
    AppState.currentDemoType = gridRow.demo_type;

    w2popup.open({
        title: "Студия записи LEMMA — Объект: " + gridRow.name,
        body: document.getElementById("studio-window-box").innerHTML,
        width: 900,
        height: 650,
        modal: true,
        onOpen(event) {
            event.done(function() {
                setTimeout(function() {
                    const workplace = document.querySelector("#w2ui-popup #studio-workplace");
                    const toolsPlace = document.querySelector("#w2ui-popup #studio-custom-tools");
                    
                    // Жестко задаем размеры контейнеру, если браузер отдал 0
                    if (workplace.clientWidth === 0) {
                        workplace.style.width = "880px";
                        workplace.style.height = "550px";
                    }

                    fetch("api.php?action=get_blob&source=object&id=" + AppState.currentSlideId)
                        .then(function(res) { return res.blob(); })
                        .then(function(blob) {
                            const classArgs = [blob];

                            if (AppState.currentDemoType === "video") {
                                AppState.activeSlideInstance = new SlideRecordVideo(classArgs);
                            } else {
                                AppState.activeSlideInstance = new SlideRecord2D(classArgs);
                            }

                            AppState.activeSlideInstance.render(workplace);
                            AppState.activeSlideInstance.renderTools(toolsPlace);

                            initStudioEvents();
                        });
                }, 150); // Чуть увеличили задержку для надежности отрисовки
            });
        },
        onClose() {
            if (AppState.activeSlideInstance) {
                AppState.activeSlideInstance.finish();
            }
            clearInterval(AppState.timerInterval);
            AppState.activeSlideInstance = null;
        }
    });
}

function initStudioEvents() {
    const btnStart = document.querySelector("#w2ui-popup #btn-start-record");
    const btnStop = document.querySelector("#w2ui-popup #btn-stop-record");
    const timerDisplay = document.querySelector("#w2ui-popup #record-timer");
    const statusTxt = document.querySelector("#w2ui-popup #studio-status");

    btnStart.onclick = function() {
        btnStart.disabled = true;
        btnStop.disabled = false;
        statusTxt.textContent = "🔴 Идет запись движений...";
        
        AppState.startTime = Date.now();
        AppState.timerInterval = setInterval(function() {
            const diff = Date.now() - AppState.startTime;
            const ms = String(diff % 1000).padStart(3, "0").slice(0, 2);
            const secs = String(Math.floor(diff / 1000) % 60).padStart(2, "0");
            const mins = String(Math.floor(diff / 60000)).padStart(2, "0");
            timerDisplay.textContent = mins + ":" + secs + "." + ms;
        }, 30);

        AppState.activeSlideInstance.start();
    };

    btnStop.onclick = function() {
        clearInterval(AppState.timerInterval);
        btnStop.disabled = true;
        statusTxt.textContent = "Запись остановлена. Сохранение...";

        AppState.activeSlideInstance.finish();
        const recordedCommands = AppState.activeSlideInstance.getCommands();

        // Заменяем ломающий стек окон w2prompt на нативный, стабильный prompt
        const val = prompt("Введите название сценария демонстрации:", "");
        
        if (val === null) {
            w2popup.close();
            return;
        }

        const formData = new FormData();
        formData.append("action", "save_scenario");
        formData.append("slide_id", AppState.currentSlideId);
        formData.append("name", val);
        formData.append("commands", JSON.stringify(recordedCommands));

        fetch("api.php", { method: "POST", body: formData })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            if (res.status === "success") {
                w2popup.close();
                w2ui.gridScenarios.reload();
                w2alert("Демонстрация успешно добавлена во вторую таблицу!");
            } else {
                w2alert(res.message);
            }
        });
    };
}

function openPlayerWindow(sectionId) {
    w2popup.open({
        title: "Воспроизведение Живой Демонстрации LEMMA",
        body: document.getElementById("player-window-box").innerHTML,
        width: 900, height: 650, modal: true,
        onOpen(event) {
            event.done(function() {
                setTimeout(function() {
                    const workplace = document.querySelector("#w2ui-popup #player-workplace");
                    const titleDisplay = document.querySelector("#w2ui-popup #player-title");
                    fetch("api.php?action=get_scenario_data&section_id=" + sectionId).then(r => r.json()).then(res => {
                        if (res.status !== "success") { w2alert(res.message); w2popup.close(); return; }
                        const scenario = res.data; titleDisplay.textContent = "Сценарий: " + scenario.name;
                        const commandsArray = JSON.parse(scenario.commands_json);
                        fetch("api.php?action=get_blob&source=command&id=" + sectionId).then(r => r.blob()).then(blob => {
                            const classArgs = [blob, commandsArray];
                            if (scenario.demo_type === "video") { AppState.activeSlideInstance = new SlideRecordVideo(...classArgs); }
                            else { AppState.activeSlideInstance = new SlideRecord2D(...classArgs); }
                            AppState.activeSlideInstance.render(workplace); initPlayerEvents();
                        });
                    });
                }, 100);
            });
        },
        onClose() { if (AppState.activeSlideInstance) { AppState.activeSlideInstance.pause(); } AppState.activeSlideInstance = null; }
    });
}

function initPlayerEvents() {
    const btnPlay = document.querySelector("#w2ui-popup #btn-player-play");
    const btnPause = document.querySelector("#w2ui-popup #btn-player-pause");
    const btnClose = document.querySelector("#w2ui-popup #btn-player-close");
    btnPlay.onclick = function() { AppState.activeSlideInstance.play(); };
    btnPause.onclick = function() { AppState.activeSlideInstance.pause(); };
    btnClose.onclick = function() { w2popup.close(); };
}