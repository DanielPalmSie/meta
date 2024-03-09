document.getElementById('upload-button').addEventListener('click', function() {
    const CHUNK_SIZE = 1000000; // 1MB
    let file = document.getElementById('file-input').files[0];
    if (!file) {
        console.log("No file selected.");
        return;
    }

    let formData = new FormData();
    formData.append('name', file.name);

    fetch('/get-uploaded-chunk-index', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
        .then(data => {
            let lastChunkIndex = data.last_chunk_index;
            let start = (lastChunkIndex + 1) * CHUNK_SIZE;
            let end = start + CHUNK_SIZE;
            let chunks = [];
            let totalChunks = Math.ceil(file.size / CHUNK_SIZE);

            while (start < file.size) {
                let chunk = file.slice(start, end);
                chunks.push(chunk);
                start = end;
                end = start + CHUNK_SIZE;
            }

            chunks.forEach((chunk, index) => {
                let actualIndex = lastChunkIndex + 1 + index;
                let formData = new FormData();
                formData.append('file', chunk);
                formData.append('name', file.name);
                formData.append('index', actualIndex);
                console.log(totalChunks);
                formData.append('totalChunks', totalChunks);

                fetch('/upload-chunks', {
                    method: 'POST',
                    body: formData
                }).then(response => {

                }).catch(error => {

                });
            });
        })
        .catch(error => {
            console.error('Error fetching last chunk index:', error);
        });
});
