<?php

namespace Meta\Project;

class FileManager {
    private $database;
    private $tempDir = __DIR__.'/../public/uploads/to/temp/dir';

    public function __construct(Database $database) {
        $this->database = $database;
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    public function getLastChunkIndex($fileId) {
        $query = "SELECT MAX(chunk_index) as last_chunk_index FROM file_chunks WHERE file_id = :file_id";
        $result = $this->database->query($query, [':file_id' => $fileId])->fetch();
        return $result ? $result['last_chunk_index'] : -1;
    }

    public function uploadChunk($fileId, $chunkIndex, $file) {
        $targetFilePath = "$this->tempDir/$fileId.part.$chunkIndex";
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            $query = "INSERT INTO file_chunks (file_id, chunk_index, chunk_size) VALUES (:file_id, :chunk_index, :chunk_size)";
            $this->database->query($query, [
                ':file_id' => $fileId,
                ':chunk_index' => $chunkIndex,
                ':chunk_size' => $file['size'],
            ]);
            return true;
        }
        return false;
    }

    public function checkAllChunksUploaded($fileId, $totalChunks) {
        $query = "SELECT COUNT(*) FROM file_chunks WHERE file_id = :file_id";
        $uploadedChunks = $this->database->query($query, [':file_id' => $fileId])->fetchColumn();
        return $uploadedChunks == $totalChunks;
    }

    public function mergeChunks($fileId, $totalChunks) {
        $finalFilePath = "$this->tempDir/$fileId";
        $handle = fopen($finalFilePath, 'wb');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFilePath = "$this->tempDir/$fileId.part.$i";
            $chunkContent = file_get_contents($chunkFilePath);
            fwrite($handle, $chunkContent);
            unlink($chunkFilePath);
        }

        fclose($handle);
        $this->clearChunks($fileId);
        return $finalFilePath;
    }

    private function clearChunks($fileId) {
        $deleteQuery = "DELETE FROM file_chunks WHERE file_id = :file_id";
        $this->database->query($deleteQuery, [':file_id' => $fileId]);
    }
}
