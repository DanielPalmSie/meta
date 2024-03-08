<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка файла с Bootstrap</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h1>Загрузка файла</h1>
    <input type="file" id="fileInput" class="form-control" />
    <button id="uploadButton" class="btn btn-primary mt-3">Загрузить</button>
    <div id="uploadStatus" class="mt-3"></div>
</div>

<!-- Optional JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js" integrity="sha384-ZzWQbF+4vQ4YSDTFxGnM2Ehb1f8Y/xhaQy4FZtFCTMGj0g4DbrF3tzT3R8RGFc3K" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8sh+Wy4Ck4SOF4y4Ck4C2DG7qdSp8KbMjJ8K8J" crossorigin="anonymous"></script>
<script src="upload.js"></script>
</body>
</html>
