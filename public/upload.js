document.getElementById('upload-button').addEventListener('click', function() {
    const CHUNK_SIZE = 1000000; // 1MB
    let file = document.getElementById('file-input').files[0];
    const uploadStatus = document.getElementById('upload-status');
    if (!file) {
        uploadStatus.innerHTML = `<div class="alert alert-danger" role="alert">No file selected.</div>`;
        return;
    }

    uploadStatus.innerHTML = `<div class="alert alert-info">Starting upload...</div>`;

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
                let actualIndex = lastChunkIndex + index;
                let formData = new FormData();
                formData.append('file', chunk);
                formData.append('name', file.name);
                formData.append('index', actualIndex);
                formData.append('totalChunks', totalChunks);

                fetch('/upload-chunks', {
                    method: 'POST',
                    body: formData
                }).then(response => {
                    if (actualIndex === totalChunks - 1) {
                        uploadStatus.innerHTML = `<div class="alert alert-success">Upload complete!</div>`;
                    } else {
                        uploadStatus.innerHTML = `<div class="alert alert-warning">Uploading chunk ${actualIndex + 1} of ${totalChunks}...</div>`;
                    }
                }).catch(error => {
                    uploadStatus.innerHTML = `<div class="alert alert-danger" role="alert">An error occurred: ${error.message}</div>`;
                });
            });
        })
        .catch(error => {
            uploadStatus.innerHTML = `<div class="alert alert-danger" role="alert">Error fetching last chunk index: ${error.message}</div>`;
        });
});
