// utils/LoadPPTasPict.js
// Модуль импорта презентаций PPTX и набора изображений в БД ММЛ

import { w2form, w2utils, w2alert } from '../lib/w2ui-2.0.es6.js';
import { getSlideTitle, getSlideNotes } from '../lib/openXML.js';
import {
    connectDB,
    newFolder,
    newScenario,
    newCard,
    setCardDemo,
    newShortcut,
    newSlide,
    setSlideDemo
} from '../dbLayer/dbMML.js';

let dirHandle = null;
let pptFileHandle = null;
let pptZip = null;
let slideImages = []; // { slideNum: number, handle: FileSystemFileHandle }
let firstSlideTitle = '';
let firstSlideNotes = '';

const logBox = document.getElementById('log-box');
const statusBadge = document.getElementById('diag-status-badge');
const dirStatus = document.getElementById('dir-status');
const btnSelectDir = document.getElementById('btn-select-dir');

function appendLog(type, message, detail = '') {
    const time = new Date().toLocaleTimeString();
    const line = document.createElement('div');
    line.className = 'log-line';

    let tagClass = 'log-tag-info';
    let tagText = '[ИНФО]';
    if (type === 'success') { tagClass = 'log-tag-ok'; tagText = '[ УСПЕХ ]'; }
    if (type === 'warn') { tagClass = 'log-tag-warn'; tagText = '[ВНИМАНИЕ]'; }
    if (type === 'error') { tagClass = 'log-tag-err'; tagText = '[ОШИБКА]'; }

    line.innerHTML = `
        <span class="log-time">[${time}]</span>
        <span class="${tagClass}">${tagText}</span>
        <span><b>${escapeHtml(message)}</b> ${detail ? `— <span style="color:#94a3b8">${escapeHtml(detail)}</span>` : ''}</span>
    `;
    logBox.appendChild(line);
    logBox.scrollTop = logBox.scrollHeight;
}

function clearLog() {
    logBox.innerHTML = '';
}

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// 1. Инициализация формы w2form
let form = new w2form({
    name: 'ppt_loader_form',
    box: '#form-container',
    fields: [
        {
            field: 'presentation_name',
            type: 'text',
            required: true,
            html: { label: 'Название презентации / темы' }
        },
        {
            field: 'create_cards',
            type: 'checkbox',
            html: { label: 'Создать Карточки в Папке' }
        },
        {
            field: 'create_scenario',
            type: 'checkbox',
            html: { label: 'Создать Сценарий со Слайдами' }
        }
    ],
    record: {
        presentation_name: 'Новая презентация',
        create_cards: true,
        create_scenario: true
    },
    actions: {
        'Запустить загрузку': async function () {
            if (this.validate().length > 0) return;

            if (!dirHandle || !pptZip || slideImages.length === 0) {
                w2alert('Пожалуйста, сначала выберите директорию с презентацией *.pptx и картинками слайдов.', 'Внимание');
                return;
            }

            const rec = this.record;
            if (!rec.create_cards && !rec.create_scenario) {
                w2alert('Необходимо выбрать хотя бы один режим загрузки (создание Карточек или Сценария).', 'Внимание');
                return;
            }

            const totalSlides = slideImages.length;
            clearLog();
            statusBadge.className = 'diag-status status-idle';
            statusBadge.innerText = `Импорт: 0 из ${totalSlides}`;

            appendLog('info', 'Старт загрузки презентации в БД ММЛ', `Всего слайдов: ${totalSlides}`);

            w2utils.lock(this.box, 'Инициализация загрузки...', true);

            try {
                let folderId = null;
                let scenarioId = null;

                // Создаем Папку для Карточек (если выбран чекбокс)
                if (rec.create_cards) {
                    let folderRes = await newFolder(rec.presentation_name, firstSlideNotes);
                    if (folderRes && folderRes.Folder_ID) {
                        folderId = folderRes.Folder_ID;
                        appendLog('success', `Создана Папка ID=${folderId}`, `«${rec.presentation_name}»`);
                    } else {
                        throw new Error('Не удалось создать Папку в БД');
                    }
                }

                // Создаем Сценарий (если выбран чекбокс)
                if (rec.create_scenario) {
                    let scenarioRes = await newScenario(rec.presentation_name, firstSlideNotes);
                    if (scenarioRes && scenarioRes.Scenario_ID) {
                        scenarioId = scenarioRes.Scenario_ID;
                        appendLog('success', `Создан Сценарий ID=${scenarioId}`, `«${rec.presentation_name}»`);
                    } else {
                        throw new Error('Не удалось создать Сценарий в БД');
                    }
                }

                // Последовательная обработка и загрузка слайдов
                for (let i = 0; i < totalSlides; i++) {
                    const slideItem = slideImages[i];
                    const slideNum = slideItem.slideNum;

                    w2utils.lock(this.box, `Слайд ${i + 1} из ${totalSlides} (${Math.round(((i + 1) / totalSlides) * 100)}%)...`, true);
                    statusBadge.innerText = `Импорт: ${i + 1} из ${totalSlides}`;

                    // 1. Извлекаем заголовок из XML
                    let slideTitle = `Слайд ${slideNum}`;
                    let slideFile = pptZip.file(`ppt/slides/slide${slideNum}.xml`);
                    if (slideFile) {
                        let xmlStr = await slideFile.async('string');
                        let parsedTitle = getSlideTitle(xmlStr);
                        if (parsedTitle && parsedTitle.trim()) {
                            slideTitle = parsedTitle.trim();
                        }
                    }

                    // 2. Извлекаем заметки из XML
                    let slideNotes = '';
                    let notesFile = pptZip.file(`ppt/notesSlides/notesSlide${slideNum}.xml`);
                    if (notesFile) {
                        let notesXmlStr = await notesFile.async('string');
                        let parsedNotes = getSlideNotes(notesXmlStr);
                        if (parsedNotes && parsedNotes.trim()) {
                            slideNotes = parsedNotes.trim();
                        }
                    }

                    // 3. Получаем бинарник изображения
                    let imageBlob = await slideItem.handle.getFile();

                    // 4. Записываем Карточку (если активно)
                    if (rec.create_cards && folderId) {
                        let cardRes = await newCard(slideTitle, slideNotes, 1); // DemoType_ID = 1 (2D Растр)
                        if (cardRes && cardRes.Card_ID) {
                            await setCardDemo(cardRes.Card_ID, imageBlob);
                            await newShortcut(cardRes.Card_ID, folderId);
                        }
                    }

                    // 5. Записываем Слайд в Сценарий (если активно)
                    if (rec.create_scenario && scenarioId) {
                        let slideData = {
                            Name: slideTitle,
                            Notes: slideNotes,
                            Scenario_ID: scenarioId,
                            Order_Num: i + 1,
                            DemoType_ID: 1
                        };
                        let slideRes = await newSlide(slideData);
                        if (slideRes && slideRes.Slide_ID) {
                            await setSlideDemo(slideRes.Slide_ID, imageBlob);
                        }
                    }

                    appendLog('success', `Слайд ${i + 1} [${slideItem.handle.name}]`, `«${slideTitle}» ${slideNotes ? '(с заметками)' : ''}`);
                }

                w2utils.unlock(this.box);
                statusBadge.className = 'diag-status status-ok';
                statusBadge.innerText = '✓ Загрузка успешно завершена';

                let summaryMsg = `Успешно загружено слайдов: <b>${totalSlides}</b>.<br><br>`;
                if (folderId) summaryMsg += `• Создана Папка с карточками (ID: <b>${folderId}</b>)<br>`;
                if (scenarioId) summaryMsg += `• Создан Сценарий (ID: <b>${scenarioId}</b>)<br>`;

                appendLog('success', 'Импорт презентации полностью завершен!', `Обработано ${totalSlides} слайдов.`);
                w2alert(summaryMsg, 'Импорт завершен');

            } catch (err) {
                w2utils.unlock(this.box);
                statusBadge.className = 'diag-status status-err';
                statusBadge.innerText = '✗ Ошибка загрузки';
                appendLog('error', 'Ошибка при выполнении импорта', err.message);
                w2alert('Произошла ошибка при загрузке: ' + err.message, 'Ошибка');
            }
        }
    }
});

// 2. Обработка выбора директории
btnSelectDir.onclick = async function () {
    try {
        if (!window.showDirectoryPicker) {
            w2alert('Ваш браузер не поддерживает File System Access API (showDirectoryPicker). Рекомендуется использовать Chrome или Edge.', 'Внимание');
            return;
        }

        dirHandle = await window.showDirectoryPicker();
        slideImages = [];
        pptFileHandle = null;
        pptZip = null;

        clearLog();
        statusBadge.className = 'diag-status status-idle';
        statusBadge.innerText = 'Анализ папки...';
        appendLog('info', 'Выбрана директория', dirHandle.name);

        // Сканируем содержимое папки
        const imageRegex = /^(?:слайд|slide)[_\-\s]*(\d+)\.(jpg|jpeg|png)$/i;

        for await (const [name, handle] of dirHandle.entries()) {
            if (handle.kind === 'file') {
                if (name.toLowerCase().endsWith('.pptx') && !name.startsWith('~$')) {
                    pptFileHandle = handle;
                } else {
                    const match = name.match(imageRegex);
                    if (match) {
                        slideImages.push({
                            slideNum: parseInt(match[1], 10),
                            handle: handle
                        });
                    }
                }
            }
        }

        // Проверка наличия PPTX
        if (!pptFileHandle) {
            dirStatus.innerHTML = `<span style="color:#dc2626;">❌ В выбранной папке не найден файл <b>*.pptx</b>.</span>`;
            statusBadge.className = 'diag-status status-err';
            statusBadge.innerText = 'PPTX не найден';
            appendLog('error', 'В директории отсутствует файл презентации *.pptx');
            return;
        }

        // Сортируем картинки по порядковому номеру слайда
        slideImages.sort((a, b) => a.slideNum - b.slideNum);

        if (slideImages.length === 0) {
            dirStatus.innerHTML = `<span style="color:#dc2626;">❌ Не найдены изображения слайдов (ожидаются файлы вида <b>Слайд1.jpg</b> или <b>Slide1.png</b>).</span>`;
            statusBadge.className = 'diag-status status-err';
            statusBadge.innerText = 'Картинки не найдены';
            appendLog('error', 'В директории не найдены изображения слайдов формата Слайд#.jpg / Slide#.png');
            return;
        }

        // Читаем PPTX через JSZip
        const pptBlob = await pptFileHandle.getFile();
        if (window.JSZip) {
            pptZip = new window.JSZip();
        } else {
            throw new Error('Библиотека JSZip не загружена');
        }
        await pptZip.loadAsync(pptBlob);

        // Анализируем слайды внутри архива
        const xmlSlides = pptZip.file(/ppt\/slides\/slide\d+\.xml/);
        const xmlSlideCount = xmlSlides.length;

        appendLog('info', `Найден файл презентации: ${pptFileHandle.name}`, `XML-слайдов: ${xmlSlideCount}, Картинок: ${slideImages.length}`);

        // Анализируем 1-й слайд для извлечения названия
        firstSlideTitle = pptFileHandle.name.replace(/\.[^/.]+$/, "");
        firstSlideNotes = '';

        let slide1 = pptZip.file('ppt/slides/slide1.xml');
        if (slide1) {
            let slide1Xml = await slide1.async('string');
            let parsedTitle = getSlideTitle(slide1Xml);
            if (parsedTitle && parsedTitle.trim()) {
                firstSlideTitle = parsedTitle.trim();
            }
        }

        let notes1 = pptZip.file('ppt/notesSlides/notesSlide1.xml');
        if (notes1) {
            let notes1Xml = await notes1.async('string');
            let parsedNotes = getSlideNotes(notes1Xml);
            if (parsedNotes && parsedNotes.trim()) {
                firstSlideNotes = parsedNotes.trim();
            }
        }

        // Обновляем запись формы
        form.record.presentation_name = firstSlideTitle;
        form.refresh();

        dirStatus.innerHTML = `✓ Найдена презентация: <b>${escapeHtml(pptFileHandle.name)}</b> (Слайдов: <b>${slideImages.length}</b>). Готово к загрузке.`;
        statusBadge.className = 'diag-status status-ok';
        statusBadge.innerText = `Готово: ${slideImages.length} слайдов`;

        appendLog('success', 'Анализ папки завершен успешно', `Заголовок темы: «${firstSlideTitle}»`);

    } catch (err) {
        console.error(err);
        dirStatus.innerHTML = `<span style="color:#dc2626;">Ошибка при открытии папки: ${escapeHtml(err.message)}</span>`;
        statusBadge.className = 'diag-status status-err';
        statusBadge.innerText = 'Ошибка открытия';
        appendLog('error', 'Исключение при анализе папки', err.message);
    }
};