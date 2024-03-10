<?php

namespace Meta\Project;

class App {
    private FileManager $fileManager;

    public function __construct() {
        $database = new Database();
        $this->fileManager = new FileManager($database);
    }

    public function getLastChunkIndex($fileName) {
        return $this->fileManager->getLastChunkIndex($fileName);
    }

    public function uploadChunk($fileName, $fileIndex, $fileContent, $totalChunks): bool|string
    {
        $success = $this->fileManager->uploadChunk($fileName, $fileIndex, $fileContent);
        if ($success && $this->fileManager->checkAllChunksUploaded($fileName, $totalChunks)) {
            return $this->fileManager->mergeChunks($fileName, $totalChunks);
        }
        return $success;
    }
}