<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Завершаем выполнение скрипта
}

require '../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = htmlspecialchars($_POST['description']);
    $targetDir = "uploads/";
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $uploadedFiles = [];

    if (!empty($_FILES['images']['name'][0]) && !empty($description)) {
        foreach ($_FILES['images']['name'] as $key => $fileName) {
            $fileTmpPath = $_FILES['images']['tmp_name'][$key];
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
            $targetFilePath = $targetDir . basename($fileName);

            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
                    $uploadedFiles[] = $targetFilePath;
                }
            }
        }

        if (count($uploadedFiles) > 1) {
            $collagePath = $targetDir . 'collage_' . time() . '.jpg';
            createCollage($uploadedFiles, $collagePath);
            $insert = "INSERT INTO gallery (file_name, description) VALUES ('" . basename($collagePath) . "', '$description')";
            mysqli_query($conn, $insert);
            $successMsg = "Коллаж успешно создан!";
        } elseif (count($uploadedFiles) === 1) {
            $insert = "INSERT INTO gallery (file_name, description) VALUES ('" . basename($uploadedFiles[0]) . "', '$description')";
            mysqli_query($conn, $insert);
            $successMsg = "Изображение успешно загружено!";
        } else {
            $errorMsg = "Ошибка при загрузке файлов.";
        }
    } else {
        $errorMsg = "Заполните все поля.";
    }
}

function createCollage($imagePaths, $outputPath)
{
    $images = array_map(fn($path) => imagecreatefromstring(file_get_contents($path)), $imagePaths);
    $width = 300 * count($images);
    $height = 300;
    $collage = imagecreatetruecolor($width, $height);

    $xOffset = 0;
    foreach ($images as $img) {
        imagecopyresized($collage, $img, $xOffset, 0, 0, 0, 300, 300, imagesx($img), imagesy($img));
        imagedestroy($img);
        $xOffset += 300;
    }

    imagejpeg($collage, $outputPath);
    imagedestroy($collage);
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить изображения</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/../includes/topbar.php'; ?>

    <div class="container mt-5">
        <header class="page-head">
            <div>
                <a class="back-link" href="../manage_gallery.php">Фото</a>
                <h1>Новые фото</h1>
                <p>Одна подпись применится ко всем выбранным снимкам.</p>
            </div>
        </header>

        <?php if (isset($successMsg)) { ?>
            <div class="alert alert-success">
                <?= $successMsg ?>
            </div>
        <?php } ?>
        <?php if (isset($errorMsg)) { ?>
            <div class="alert alert-danger">
                <?= $errorMsg ?>
            </div>
        <?php } ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Описание</label>
                <textarea class="form-control" name="description" rows="3" placeholder="Введите описание"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Изображения</label>
                <input class="form-control" type="file" name="images[]" multiple required>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Добавить</button>
                <a href="../manage_gallery.php" class="btn btn-light">Назад</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>