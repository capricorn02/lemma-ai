// dbLib.js

// Инициализация подключения к БД при старте приложения
export async function connectDB() {
    let response = await fetch('/dbLib/connectDB.php');
    if (response.ok) {
        return await response.json(); // Вернет {status: true} или ошибку
    }
    return false;
}

export async function newCard(name) {
    let formData = new FormData();
    formData.append('Name', name);
    let response = await fetch('/dbLib/NewCard.php', { method: 'POST', body: formData });
    return response.ok ? await response.json() : false;
}

export async function setCardBlob(cardId, blobFile) {
    let formData = new FormData();
    formData.append('Card_ID', cardId);
    formData.append('BlobFile', blobFile);
    let response = await fetch('/dbLib/SetCardBlob.php', { method: 'POST', body: formData });
    return response.ok ? await response.json() : false;
}

export async function newFolder(name) {
    let formData = new FormData();
    formData.append('Name', name);
    let response = await fetch('/dbLib/NewFolder.php', { method: 'POST', body: formData });
    return response.ok ? await response.json() : false;
}

export async function setLinkLabel(cardId, folderId) {
    let formData = new FormData();
    formData.append('Card_ID', cardId);
    formData.append('Folder_ID', folderId);
    let response = await fetch('/dbLib/SetLinkLabel.php', { method: 'POST', body: formData });
    return response.ok ? await response.json() : false;
}