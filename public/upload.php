<?php

require __DIR__ . '/../vendor/autoload.php';

use Meta\Project\App;

$app = new App();

$fileName = $_POST['name'] ?? null;
$fileIndex = $_POST['index'] ?? null;
$totalChunks = $_POST['totalChunks'] ?? null;
$fileContent = $_FILES['file'] ?? null;

if ($fileName && $fileIndex !== null && $totalChunks && $fileContent) {
    $finalFilePath = $app->uploadChunk($fileName, $fileIndex, $fileContent, $totalChunks);
    if ($finalFilePath) {
        echo json_encode(['path' => $finalFilePath]);
    } else {
        echo json_encode(['message' => 'Chunk uploaded, pending final assembly.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields.']);
}
