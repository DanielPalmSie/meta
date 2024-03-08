<?php

$pdo = new PDO('pgsql:host=postgres;dbname=your_db', 'your_username', 'your_password');

// Предположим, что у вас уже есть PDO подключение к базе данных
// $pdo = new PDO('pgsql:host=your_host;dbname=your_db', 'your_username', 'your_password');

// Установите заголовки для ответа в формате JSON
header('Content-Type: application/json');

// Предположим, что входные данные поступают в формате JSON
$inputData = json_decode(file_get_contents('php://input'), true);
$fileName = $inputData['fileName'];

// Инициализируйте индекс последнего загруженного чанка как -1, что указывает на отсутствие загрузок
$lastChunkIndex = -1;

try {
    // Подготовьте запрос к базе данных для получения максимального индекса чанка для заданного файла
    $stmt = $pdo->prepare("SELECT MAX(chunk_index) as last_chunk_index FROM chunk_status WHERE file_name = :fileName");
    $stmt->execute(['fileName' => $fileName]);

    // Получите результат
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Если результат найден, обновите $lastChunkIndex
    if ($result && $result['last_chunk_index'] !== null) {
        $lastChunkIndex = (int)$result['last_chunk_index'];
    }

    // Отправьте индекс последнего загруженного чанка в ответе
    echo json_encode(['lastUploadedChunkIndex' => $lastChunkIndex]);
} catch (PDOException $e) {
    // Если произошла ошибка, отправьте код состояния 500
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
