<?php
session_start();  // Начинаем сессию для передачи сообщений
ob_start(); // Начало буферизации вывода

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Завершаем выполнение скрипта
}

require '../../config.php';

// Устанавливаем кодировку соединения
mysqli_set_charset($conn, 'utf8mb4');

// Проверяем соединение с базой данных
if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Папка для загрузки изображений
    $target_dir = "assets/projects/";

    // Проверяем, существует ли папка для изображений, если нет - создаём
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);  // Создаем папку с правами 0777
    }

    // Загружаем основное изображение проекта
    $main_image = $_FILES['main_image']['name'];
    $main_image_path = $target_dir . basename($main_image);

    // Проверка загрузки основного изображения
    if (!move_uploaded_file($_FILES["main_image"]["tmp_name"], $main_image_path)) {
        echo "Ошибка при загрузке основного изображения проекта!<br>";
        exit;
    }

    // Загружаем дополнительные изображения для галереи
    $gallery_paths = [];
    if (isset($_FILES['gallery']['name']) && count($_FILES['gallery']['name']) > 0) {
        foreach ($_FILES['gallery']['name'] as $key => $file) {
            $gallery_path = $target_dir . basename($file);

            // Проверка загрузки каждого изображения
            if (!move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $gallery_path)) {
                echo "Галерея: $file не загружено!<br>";
            } else {
                $gallery_paths[] = basename($file); // Добавляем только имя файла
                echo "Галерея: $file загружено успешно!<br>";
            }
        }
    }

    // Преобразуем массив путей галереи в строку
    $gallery = implode(",", $gallery_paths); // Строка путей разделенная запятой

    // Сохраняем в базу
    $query = "INSERT INTO projects (name, description, main_image, gallery) VALUES ('$name', '$description', '$main_image', '$gallery')";

    if (mysqli_query($conn, $query)) {
        // Сообщение об успешном добавлении
        $_SESSION['message'] = 'Проект успешно добавлен!';
        $_SESSION['message_type'] = 'success';  // Тип сообщения: успех
    } else {
        // Сообщение об ошибке SQL
        $_SESSION['message'] = 'Ошибка при добавлении проекта: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';  // Тип сообщения: ошибка
    }

    // Перенаправление на страницу manage_projects.php
    header('Location: admin/manage_projects.php');
    exit;  // Завершаем выполнение скрипта
}
ob_end_flush(); // Завершаем буферизацию вывода
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить проект</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<script>
        // Скрипт для скрытия сообщения через 1 секунду
        window.onload = function() {
            const messageElement = document.getElementById('message');
            if (messageElement) {
                setTimeout(function() {
                    messageElement.style.display = 'none';
                }, 1000); // 1 секунда
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <h1 class="h2 mt-5">Добавить проект</h1>

        <!-- Выводим сообщение, если оно существует -->
        <?php if (isset($_SESSION['message'])): ?>
            <div id="message" class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php
            // Очищаем сообщение после отображения 
            unset($_SESSION['message'], $_SESSION['message_type']);
            ?>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Название проекта</label>
                <input class="form-control" type="text" name="name" required>
                </div>

            <div class="mb-3">
                <label class="form-label">Описание</label>
                <textarea class="form-control" name="description" required></textarea>
                </div>

            <div class="mb-3">
                <label class="form-label">Основное изображение</label>
                <input class="form-control" type="file" name="main_image" accept="image/*" required>
                </div>

            <div class="mb-3">
                <label class="form-label">Галерея (несколько изображений)</label>
                <input class="form-control" type="file" name="gallery[]" accept="image/*" multiple>
                </div>

            <div class="mb-3">
                <button class="btn btn-primary" type="submit">Добавить</button>
                </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>