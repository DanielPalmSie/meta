<?php

$tempDir = __DIR__.'/uploads/to/temp/dir'; // Укажите путь к временной директории
$fileName = $_POST['name'];
$fileIndex = $_POST['index'];

if (!is_dir($tempDir)) {
    mkdir($tempDir, 0777, true);
}

move_uploaded_file($_FILES['file']['tmp_name'], "$tempDir/$fileName.part.$fileIndex");

// Скрипт для объединения чанков (см. следующий шаг) может быть вызван здесь после загрузки последнего чанка
// Этот код предполагает, что вы знаете, когда загружены все чанки,
// например, после загрузки чанка с максимальным индексом

$finalDir = __DIR__.'/uploads/to/final/dir'; // Укажите путь к директории для итогового файла

/*if (!is_dir($finalDir)) {
    mkdir($tempDir, 0777, true);
}

$filePath = "$tempDir/$fileName.part.*";
$fileParts = glob($filePath);
sort($fileParts, SORT_NATURAL);

$finalFile = fopen("$finalDir/$fileName", 'wb');

foreach ($fileParts as $filePart) {
    $chunk = file_get_contents($filePart);
    fwrite($finalFile, $chunk);
    unlink($filePart); // Опционально: удаление чанка после его добавления
}

fclose($finalFile);*/
