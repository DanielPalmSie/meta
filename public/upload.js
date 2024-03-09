document.getElementById('upload-button').addEventListener('click', function() {
    const CHUNK_SIZE = 1000000; // 1MB
    let file = document.getElementById('file-input').files[0];
    if (!file) {
        console.log("No file selected.");
        return;
    }

    // Создаем FormData объект для отправки fileName на сервер
    let formData = new FormData();
    formData.append('name', file.name);

    // Отправляем запрос для получения последнего индекса загруженного чанка
    fetch('/get-uploaded-chunk-index', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
        .then(data => {
            let lastChunkIndex = data.last_chunk_index;
            let start = (lastChunkIndex + 1) * CHUNK_SIZE;
            let end = start + CHUNK_SIZE;
            let chunks = [];

            while (start < file.size) {
                let chunk = file.slice(start, end);
                chunks.push(chunk);
                start = end;
                end = start + CHUNK_SIZE;
            }

            // Загружаем чанки начиная с последнего индекса
            chunks.forEach((chunk, index) => {
                let actualIndex = lastChunkIndex + 1 + index;
                let formData = new FormData();
                formData.append('file', chunk);
                formData.append('name', file.name);
                formData.append('index', actualIndex);

                fetch('/upload-chunks', { // Убедитесь, что этот URL верный для загрузки чанков
                    method: 'POST',
                    body: formData
                }).then(response => {
                    // Обработка ответа
                }).catch(error => {
                    // Обработка ошибки
                });
            });
        })
        .catch(error => {
            console.error('Error fetching last chunk index:', error);
        });
});
