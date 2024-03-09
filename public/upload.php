<?php

$pdo = new PDO('pgsql:host=postgres;dbname=postgres', 'your_username', 'your_password');

$tempDir = __DIR__.'/uploads/to/temp/dir'; // Укажите путь к временной директории
$fileName = $_POST['name'];
$fileIndex = $_POST['index'];
$totalChunks = $_POST['totalChunks']; // Получаем общее количество чанков из запроса

if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true);
}

$targetFilePath = "$tempDir/$fileName.part.$fileIndex";
if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
    // Файл успешно загружен, теперь добавляем информацию о чанке в базу данных
    $query = "INSERT INTO file_chunks (file_id, chunk_index, chunk_size) VALUES (:file_id, :chunk_index, :chunk_size)";
    $statement = $pdo->prepare($query);

    $statement->execute([
        ':file_id' => $fileName,
        ':chunk_index' => $fileIndex,
        ':chunk_size' => $_FILES['file']['size'],
    ]);

    // Проверяем, соответствует ли количество загруженных чанков общему количеству чанков
    $query = "SELECT COUNT(*) FROM file_chunks WHERE file_id = :file_id";
    $statement = $pdo->prepare($query);
    $statement->execute([':file_id' => $fileName]);
    $uploadedChunks = $statement->fetchColumn();

    if ($uploadedChunks == $totalChunks) {
        // Если все чанки загружены, начинаем процесс склеивания
        $finalFilePath = "$tempDir/$fileName";
        $handle = fopen($finalFilePath, 'wb');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFilePath = "$tempDir/$fileName.part.$i";
            $chunkContent = file_get_contents($chunkFilePath);
            fwrite($handle, $chunkContent);
            unlink($chunkFilePath); // Удаляем чанк после добавления его содержимого
        }

        fclose($handle);
        // Удаляем информацию о чанках из базы данных
        $deleteQuery = "DELETE FROM file_chunks WHERE file_id = :file_id";
        $deleteStatement = $pdo->prepare($deleteQuery);
        $deleteStatement->execute([':file_id' => $fileName]);

        // Теперь у вас есть полный файл в $finalFilePath
        // Вы можете переместить его куда угодно или сделать доступным для скачивания
    }
}
