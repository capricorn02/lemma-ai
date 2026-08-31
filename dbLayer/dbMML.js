// dbLayer/dbMML.js
// Модуль API для работы со слоем данных МультиМедиа Лекций (MML)

const BASE_URL = '/dbLayer/dbMML/';

// ==========================================
// 1. ПОДКЛЮЧЕНИЕ К БАЗЕ ДАННЫХ
// ==========================================

export async function connectDB(host = 'MySQL-8.2', db = 'demotest', user = 'root', password = '') {
    try {
        let params = new URLSearchParams({
            DB_HOST: host,
            DB_NAME: db,
            DB_USER: user,
            DB_PASSWORD: password
        });
        let response = await fetch(`${BASE_URL}connectDB_MML.php?${params.toString()}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('connectDB error:', e);
        return false;
    }
}

export async function reconnectDB() {
    try {
        let response = await fetch(`${BASE_URL}reconnectDB_MML.php`);
        return response.ok;
    } catch (e) {
        console.error('reconnectDB error:', e);
        return false;
    }
}

export async function recreateDB(host, db, user, password) {
    try {
        let params = new URLSearchParams();
        if (host) params.append('DB_HOST', host);
        if (db) params.append('DB_NAME', db);
        if (user) params.append('DB_USER', user);
        if (password) params.append('DB_PASSWORD', password);

        let response = await fetch(`${BASE_URL}RecreateDB_MML.php?${params.toString()}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('recreateDB error:', e);
        return false;
    }
}

// ==========================================
// 2. ДЕМО-ТИПЫ (demotypes)
// ==========================================

export async function getDemoType(id) {
    try {
        let response = await fetch(`${BASE_URL}getDemoType.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('getDemoType error:', e);
        return false;
    }
}

export async function getDemoTypeList() {
    try {
        let response = await fetch(`${BASE_URL}getDemoTypeList.php`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('getDemoTypeList error:', e);
        return false;
    }
}

// ==========================================
// 3. КАРТОЧКИ (cards)
// ==========================================

export async function newCard(name = 'Новая карточка', notes = '', demoTypeId = null) {
    try {
        let formData = new FormData();
        formData.append('Name', name);
        formData.append('Notes', notes);
        if (demoTypeId) formData.append('DemoType_ID', demoTypeId);

        let response = await fetch(`${BASE_URL}newCard.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('newCard error:', e);
        return false;
    }
}

export async function setCard(card) {
    try {
        let formData = new FormData();
        formData.append('Card', JSON.stringify(card));
        let response = await fetch(`${BASE_URL}setCard.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setCard error:', e);
        return false;
    }
}

export async function getCard(id) {
    try {
        let response = await fetch(`${BASE_URL}getCard.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('getCard error:', e);
        return false;
    }
}

export async function setCardDemo(id, blob) {
    try {
        let formData = new FormData();
        formData.append('ID', id);
        formData.append('Demo', blob, 'demo.dat');
        let response = await fetch(`${BASE_URL}setCardDemo.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setCardDemo error:', e);
        return false;
    }
}

export const setCardBody = setCardDemo; // Алиас для совместимости

export async function getCardDemo(id) {
    try {
        let response = await fetch(`${BASE_URL}getCardDemo.php?ID=${id}`);
        if (response.ok) {
            return await response.blob();
        }
        return false;
    } catch (e) {
        console.error('getCardDemo error:', e);
        return false;
    }
}

export const getCardBody = getCardDemo; // Алиас для совместимости

export async function delCard(id) {
    try {
        let response = await fetch(`${BASE_URL}delCard.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('delCard error:', e);
        return false;
    }
}

// ==========================================
// 4. ПАПКИ (folders)
// ==========================================

export async function newFolder(name = 'Новая папка', notes = '') {
    try {
        let formData = new FormData();
        formData.append('Name', name);
        formData.append('Notes', notes);

        let response = await fetch(`${BASE_URL}newFolder.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('newFolder error:', e);
        return false;
    }
}

export async function setFolder(folder) {
    try {
        let formData = new FormData();
        formData.append('Folder', JSON.stringify(folder));
        let response = await fetch(`${BASE_URL}setFolder.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setFolder error:', e);
        return false;
    }
}

export async function getFolder(id) {
    try {
        let response = await fetch(`${BASE_URL}getFolder.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('getFolder error:', e);
        return false;
    }
}

export async function getFolderList() {
    try {
        let response = await fetch(`${BASE_URL}getFolderList.php`);
        if (response.ok) {
            return await response.json();
        }
        return [];
    } catch (e) {
        console.error('getFolderList error:', e);
        return [];
    }
}

export async function getFolderCards(folderId) {
    try {
        let response = await fetch(`${BASE_URL}getFolderCards.php?ID=${folderId}`);
        if (response.ok) {
            return await response.json();
        }
        return [];
    } catch (e) {
        console.error('getFolderCards error:', e);
        return [];
    }
}

export async function delFolder(id) {
    try {
        let response = await fetch(`${BASE_URL}delFolder.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('delFolder error:', e);
        return false;
    }
}

// ==========================================
// 5. ЯРЛЫКИ (shortcuts)
// ==========================================

export async function newShortcut(cardId, folderId) {
    try {
        let formData = new FormData();
        formData.append('Card_ID', cardId);
        formData.append('Folder_ID', folderId);

        let response = await fetch(`${BASE_URL}newShortcut.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('newShortcut error:', e);
        return false;
    }
}

export async function delShortcut(cardId, folderId) {
    try {
        let formData = new FormData();
        formData.append('Card_ID', cardId);
        formData.append('Folder_ID', folderId);

        let response = await fetch(`${BASE_URL}delShortcut.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('delShortcut error:', e);
        return false;
    }
}

// ==========================================
// 6. СЦЕНАРИИ (scenarios)
// ==========================================

export async function newScenario(name = 'Новый сценарий', notes = '') {
    try {
        let formData = new FormData();
        formData.append('Name', name);
        formData.append('Notes', notes);

        let response = await fetch(`${BASE_URL}newScenario.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('newScenario error:', e);
        return false;
    }
}

export async function setScenario(scenario) {
    try {
        let formData = new FormData();
        formData.append('Scenario', JSON.stringify(scenario));

        let response = await fetch(`${BASE_URL}setScenario.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setScenario error:', e);
        return false;
    }
}

export async function getScenario(id) {
    try {
        let response = await fetch(`${BASE_URL}getScenario.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('getScenario error:', e);
        return false;
    }
}

export async function getScenarioList() {
    try {
        let response = await fetch(`${BASE_URL}getScenarioList.php`);
        if (response.ok) {
            return await response.json();
        }
        return [];
    } catch (e) {
        console.error('getScenarioList error:', e);
        return [];
    }
}

export async function getScenarioSlides(scenarioId) {
    try {
        let response = await fetch(`${BASE_URL}getScenarioSlides.php?ID=${scenarioId}`);
        if (response.ok) {
            return await response.json();
        }
        return [];
    } catch (e) {
        console.error('getScenarioSlides error:', e);
        return [];
    }
}

export async function delScenario(id) {
    try {
        let response = await fetch(`${BASE_URL}delScenario.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('delScenario error:', e);
        return false;
    }
}

export async function addCard2Scenario(cardId, scenarioId) {
    try {
        let formData = new FormData();
        formData.append('Card_ID', cardId);
        formData.append('Scenario_ID', scenarioId);

        let response = await fetch(`${BASE_URL}addCard2Scenario.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('addCard2Scenario error:', e);
        return false;
    }
}

// ==========================================
// 7. СЛАЙДЫ (slides)
// ==========================================

export async function newSlide(slide) {
    try {
        let formData = new FormData();
        formData.append('Slide', JSON.stringify(slide));

        let response = await fetch(`${BASE_URL}newSlide.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('newSlide error:', e);
        return false;
    }
}

export async function setSlide(slide) {
    try {
        let formData = new FormData();
        formData.append('Slide', JSON.stringify(slide));

        let response = await fetch(`${BASE_URL}setSlide.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setSlide error:', e);
        return false;
    }
}

export async function getSlide(id) {
    try {
        let response = await fetch(`${BASE_URL}getSlide.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('getSlide error:', e);
        return false;
    }
}

export async function getSlideDemo(id) {
    try {
        let response = await fetch(`${BASE_URL}getSlideDemo.php?ID=${id}`);
        if (response.ok) {
            return await response.blob();
        }
        return false;
    } catch (e) {
        console.error('getSlideDemo error:', e);
        return false;
    }
}

export const getSlideBody = getSlideDemo; // Алиас

export async function setSlideDemo(id, blob) {
    try {
        let formData = new FormData();
        formData.append('ID', id);
        formData.append('Demo', blob, 'demo.dat');

        let response = await fetch(`${BASE_URL}setSlideDemo.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setSlideDemo error:', e);
        return false;
    }
}

export const setSlideBody = setSlideDemo; // Алиас

export async function delSlide(id) {
    try {
        let response = await fetch(`${BASE_URL}delSlide.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('delSlide error:', e);
        return false;
    }
}

// ==========================================
// 8. ЛЕКЦИИ (lectures)
// ==========================================

export async function newLecture(name = 'Новая лекция', notes = '') {
    try {
        let formData = new FormData();
        formData.append('Name', name);
        formData.append('Notes', notes);

        let response = await fetch(`${BASE_URL}newLecture.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('newLecture error:', e);
        return false;
    }
}

export async function setLecture(lecture) {
    try {
        let formData = new FormData();
        formData.append('Lecture', JSON.stringify(lecture));

        let response = await fetch(`${BASE_URL}setLecture.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setLecture error:', e);
        return false;
    }
}

export async function getLecture(id) {
    try {
        let response = await fetch(`${BASE_URL}getLecture.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('getLecture error:', e);
        return false;
    }
}

export async function getLectureList() {
    try {
        let response = await fetch(`${BASE_URL}getLectureList.php`);
        if (response.ok) {
            return await response.json();
        }
        return [];
    } catch (e) {
        console.error('getLectureList error:', e);
        return [];
    }
}

export async function getLectureSections(lectureId) {
    try {
        let response = await fetch(`${BASE_URL}getLectureSections.php?ID=${lectureId}`);
        if (response.ok) {
            return await response.json();
        }
        return [];
    } catch (e) {
        console.error('getLectureSections error:', e);
        return [];
    }
}

export async function delLecture(id) {
    try {
        let response = await fetch(`${BASE_URL}delLecture.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('delLecture error:', e);
        return false;
    }
}

export async function addLectureSection(lectureId, name = 'Новый раздел', notes = '', demoTypeId = null) {
    try {
        let formData = new FormData();
        formData.append('Lecture_ID', lectureId);
        formData.append('Name', name);
        formData.append('Notes', notes);
        if (demoTypeId) formData.append('DemoType_ID', demoTypeId);

        let response = await fetch(`${BASE_URL}addLectureSection.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('addLectureSection error:', e);
        return false;
    }
}

// ==========================================
// 9. РАЗДЕЛЫ (sections)
// ==========================================

export async function newSection(section) {
    try {
        let formData = new FormData();
        formData.append('Section', JSON.stringify(section));

        let response = await fetch(`${BASE_URL}newSection.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('newSection error:', e);
        return false;
    }
}

export async function setSection(section) {
    try {
        let formData = new FormData();
        formData.append('Section', JSON.stringify(section));

        let response = await fetch(`${BASE_URL}setSection.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setSection error:', e);
        return false;
    }
}

export async function getSection(id) {
    try {
        let response = await fetch(`${BASE_URL}getSection.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('getSection error:', e);
        return false;
    }
}

export async function getSectionDemo(id) {
    try {
        let response = await fetch(`${BASE_URL}getSectionDemo.php?ID=${id}`);
        if (response.ok) {
            return await response.blob();
        }
        return false;
    } catch (e) {
        console.error('getSectionDemo error:', e);
        return false;
    }
}

export const getSectionBody = getSectionDemo; // Алиас

export async function setSectionDemo(id, blob) {
    try {
        let formData = new FormData();
        formData.append('ID', id);
        formData.append('Demo', blob, 'demo.dat');

        let response = await fetch(`${BASE_URL}setSectionDemo.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setSectionDemo error:', e);
        return false;
    }
}

export const setSectionBody = setSectionDemo; // Алиас

export async function getSectionVideo(id) {
    try {
        let response = await fetch(`${BASE_URL}getSectionVideo.php?ID=${id}`);
        if (response.ok) {
            return await response.blob();
        }
        return false;
    } catch (e) {
        console.error('getSectionVideo error:', e);
        return false;
    }
}

export async function setSectionVideo(id, blob) {
    try {
        let formData = new FormData();
        formData.append('ID', id);
        formData.append('Video', blob, 'video.dat');

        let response = await fetch(`${BASE_URL}setSectionVideo.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setSectionVideo error:', e);
        return false;
    }
}

export async function setSectionCommands(id, commands) {
    try {
        let formData = new FormData();
        formData.append('ID', id);
        formData.append('Commands', typeof commands === 'string' ? commands : JSON.stringify(commands));

        let response = await fetch(`${BASE_URL}setSectionCommands.php`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('setSectionCommands error:', e);
        return false;
    }
}

export const setSectionControls = setSectionCommands; // Алиас

export async function delSection(id) {
    try {
        let response = await fetch(`${BASE_URL}delSection.php?ID=${id}`);
        if (response.ok) {
            return await response.json();
        }
        return false;
    } catch (e) {
        console.error('delSection error:', e);
        return false;
    }
}
