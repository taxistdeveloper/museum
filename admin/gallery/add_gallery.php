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
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="title has-text-centered">Добавить изображения</h1>

        <?php if (isset($successMsg)) { ?>
            <div class="notification is-success">
                <?= $successMsg ?>
            </div>
        <?php } ?>
        <?php if (isset($errorMsg)) { ?>
            <div class="notification is-danger">
                <?= $errorMsg ?>
            </div>
        <?php } ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="field">
                <label class="label">Описание</label>
                <div class="control">
                    <textarea class="textarea" name="description" placeholder="Введите описание"></textarea>
                </div>
            </div>
            <div class="field">
                <label class="label">Выберите изображения</label>
                <div class="control">
                    <input class="input" type="file" name="images[]" multiple required>
                </div>
            </div>
            <div class="field is-grouped is-justify-content-center mt-4">
                <button type="submit" class="button is-link">Добавить</button>
                <a href="../manage_gallery.php" class="button is-light">Назад</a>
            </div>
        </form>
    </div>
</body>

</html>