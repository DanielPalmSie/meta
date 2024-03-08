document.getElementById('upload-button').addEventListener('click', function() {
    const CHUNK_SIZE = 1000000; // 1MB
    let start = 0;
    let end = CHUNK_SIZE;
    let chunks = [];
    let file = document.getElementById('file-input').files[0];

    while (start < file.size) {
        let chunk = file.slice(start, end);
        chunks.push(chunk);
        start = end;
        end = start + CHUNK_SIZE;
        console.log(CHUNK_SIZE);
    }

    chunks.forEach((chunk, index) => {
        let formData = new FormData();
        formData.append('file', chunk);
        formData.append('name', file.name);
        formData.append('index', index);

        fetch('/get-last-uploaded-chunk-index', {
            method: 'POST',
            body: formData
        }).then(response => {
            // Обработка ответа
        }).catch(error => {
            // Обработка ошибки
        });
    });
});
