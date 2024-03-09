<?php

require __DIR__ . '/../vendor/autoload.php';

use Meta\Project\Database;
use Meta\Project\FileManager;

$database = new Database();
$fileManager = new FileManager($database);

$fileName = $_POST['name'];
$fileIndex = $_POST['index'];
$totalChunks = $_POST['totalChunks'];

if ($fileManager->uploadChunk($fileName, $fileIndex, $_FILES['file'])) {
    if ($fileManager->checkAllChunksUploaded($fileName, $totalChunks)) {
        $finalFilePath = $fileManager->mergeChunks($fileName, $totalChunks);
    }
}
