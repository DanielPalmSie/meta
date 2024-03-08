<?php

$pdo = new PDO('pgsql:host=postgres;dbname=your_db', 'your_username', 'your_password');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['file'];
    $chunkIndex = $_POST['chunkIndex'];
    $totalChunks = $_POST['totalChunks']; // Общее количество чанков
    $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $_FILES['file']['name']);
    $filePath = "uploads/{$fileName}";

    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Обработка загруженного чанка
    $tempPath = "uploads/temp_{$fileName}_chunk_{$chunkIndex}";
    move_uploaded_file($file['tmp_name'], $tempPath);

    // Вставка или обновление информации о загруженном чанке в базе данных
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO chunk_status (file_name, chunk_index, chunk_size) VALUES (?, ?, ?) ON CONFLICT (file_name, chunk_index) DO UPDATE SET chunk_size = ?");
        $stmt->execute([$fileName, $chunkIndex, $file['size'], $file['size']]);

        $pdo->commit();

        // Проверка, загружены ли все чанки
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM chunk_status WHERE file_name = ?");
        $stmt->execute([$fileName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result && $result['count'] == $totalChunks) {
            // Все чанки загружены, начинаем процесс слияния
            mergeChunks($fileName, $totalChunks);
        }

        echo "Chunk $chunkIndex from file $fileName uploaded successfully";
    } catch (PDOException $e) {
        $pdo->rollBack();
        header('HTTP/1.1 500 Internal Server Error');
        echo "Error: " . $e->getMessage();
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo "Invalid request method.";
}

// Функция слияния чанков
function mergeChunks($fileName, $totalChunks) {
    $filePath = "uploads/{$fileName}";
    $finalFile = fopen($filePath, 'wb');

    for ($i = 0; $i < $totalChunks; $i++) {
        $chunkFile = "uploads/temp_{$fileName}_chunk_{$i}";
        $chunkData = file_get_contents($chunkFile);
        fwrite($finalFile, $chunkData);
        // Удаляем временный файл чанка
        unlink($chunkFile);
    }

    fclose($finalFile);
}