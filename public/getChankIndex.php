<?php

$pdo = new PDO('pgsql:host=postgres;dbname=postgres', 'your_username', 'your_password');

// Получаем $fileName из запроса
$fileName = $_POST['name'] ?? ''; // Используйте соответствующий метод запроса (GET, POST)

if ($fileName) {
    // Подготавливаем и выполняем SQL-запрос для поиска максимального индекса чанка для заданного file_id
    $query = "SELECT MAX(chunk_index) as last_chunk_index FROM file_chunks WHERE file_id = :file_id";
    $statement = $pdo->prepare($query);
    $statement->execute([':file_id' => $fileName]);
    $result = $statement->fetch();

    // Проверяем, найдена ли информация о чанках
    if ($result && $result['last_chunk_index'] !== null) {
        // Возвращаем индекс последнего чанка
        echo json_encode(['last_chunk_index' => $result['last_chunk_index']]);
    } else {
        // Если информации о чанках нет, возвращаем индекс -1 (значит, файл не начинал загружаться)
        echo json_encode(['last_chunk_index' => -1]);
    }
} else {
    // Если fileName не предоставлен
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'File name is required.']);
}
