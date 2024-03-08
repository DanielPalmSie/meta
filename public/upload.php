<?php

$pdo = new PDO('pgsql:host=postgres;dbname=postgres', 'your_username', 'your_password');

$tempDir = __DIR__.'/uploads/to/temp/dir'; // Укажите путь к временной директории
$fileName = $_POST['name'];
$fileIndex = $_POST['index'];
$fileSize = $_FILES['file']['size']; // Получаем размер файла

if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true);
}

$targetFilePath = "$tempDir/$fileName.part.$fileIndex";
if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
    // Файл успешно загружен, теперь добавляем информацию о чанке в базу данных
    $query = "INSERT INTO file_chunks (file_id, chunk_index, chunk_size, chunk_data) VALUES (:file_id, :chunk_index, :chunk_size, :chunk_data)";
    $statement = $pdo->prepare($query);

    $statement->execute([
        ':file_id' => $fileName, // Используем имя файла как идентификатор файла
        ':chunk_index' => $fileIndex,
        ':chunk_size' => $fileSize,
        ':chunk_data' => null // Если вы храните данные чанка в файловой системе, оставьте это поле как null или уберите его
    ]);


}
