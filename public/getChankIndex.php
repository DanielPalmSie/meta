<?php

require __DIR__ . '/../vendor/autoload.php';

use Meta\Project\App;

$app = new App();

$fileName = $_POST['name'] ?? '';
if ($fileName) {
    $lastChunkIndex = $app->getLastChunkIndex($fileName);
    echo json_encode(['last_chunk_index' => $lastChunkIndex]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'File name is required.']);
}
