<?php

require __DIR__ . '/../vendor/autoload.php';

use Meta\Project\App;

$app = new App();

$path = $_SERVER['REQUEST_URI'];

switch ($path) {
    case '/upload-chunks':

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
        break;

    case '/get-uploaded-chunk-index':

        $fileName = $_POST['name'] ?? '';

        if ($fileName) {
            $lastChunkIndex = $app->getLastChunkIndex($fileName);
            echo json_encode(['last_chunk_index' => $lastChunkIndex]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'File name is required.']);
        }
        break;

    default:

        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;
}
