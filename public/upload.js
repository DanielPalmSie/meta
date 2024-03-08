document.getElementById('fileInput').addEventListener('change', handleFileUpload);

async function handleFileUpload(event) {
    const file = event.target.files[0];
    const chunkSize = 1024 * 1024; // размер каждого чанка (1МБ)
    let start = 0;

    updateStatus("Начало загрузки...");
    const totalChunks = Math.ceil(file.size / chunkSize);
    let lastUploadedChunkIndex = await getLastUploadedChunkIndex(file.name);

    for (let index = lastUploadedChunkIndex + 1; start < file.size; index++) {
        const chunk = file.slice(start, start + chunkSize);
        try {
            await uploadChunk(chunk, index, file.name, totalChunks);
            updateStatus(`Загружено чанков: ${index + 1} из ${totalChunks}`);
        } catch (error) {
            updateStatus(`Ошибка при загрузке чанка номер: ${index}`);
            console.error('Ошибка при загрузке чанка: ', error);
            break; // Прерывание цикла и остановка загрузки
        }
        start += chunkSize;
    }

    updateStatus("Загрузка файла завершена.");
}

async function getLastUploadedChunkIndex(fileName) {
    // Запрос к серверу для получения индекса последнего успешно загруженного чанка
    const response = await fetch('/get-last-uploaded-chunk-index', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ fileName }),
    });
    const data = await response.json();
    return data.lastUploadedChunkIndex || 0;
}

async function uploadChunk(chunk, chunkIndex, fileName, totalChunks, retries = 3) {
    const formData = new FormData();
    formData.append('file', chunk);
    formData.append('chunkIndex', chunkIndex);
    formData.append('fileName', fileName); // Добавлено имя файла в данные формы
    formData.append('totalChunks', totalChunks); // Добавлено общее количество чанков в данные формы

    try {
        const response = await fetch('/upload-endpoint', {
            method: 'POST',
            body: formData,
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
    } catch (error) {
        if (retries > 0) {
            console.log(`Попытка повторной загрузки чанка ${chunkIndex}, осталось попыток: ${retries}`);
            await new Promise(resolve => setTimeout(resolve, 1000)); // Подождите 1 секунду перед повторной попыткой
            await uploadChunk(chunk, chunkIndex, fileName, totalChunks, retries - 1);
        } else {
            throw error; // Бросить ошибку дальше, если попытки закончились
        }
    }
}

function updateStatus(message) {
    document.getElementById('uploadStatus').textContent = message;
}
