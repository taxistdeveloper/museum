<?php
session_start();  // Начинаем сессию для передачи сообщений
// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Завершаем выполнение скрипта
}
require '../../config.php';

// Проверяем соединение с базой данных
if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $biography = mysqli_real_escape_string($conn, $_POST['biography']);

    // Папка для загрузки изображений
    $target_dir = "assets/images/";

    // Проверяем, существует ли папка для изображений, если нет - создаём
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);  // Создаем папку с правами 0777
    }

    // Загружаем основное фото
    $photo = $_FILES['photo']['name'];
    $target_file = $target_dir . basename($photo);

    // Проверка загрузки основного фото
    if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        echo "Ошибка при загрузке фото!<br>";
        exit;
    }

    // Загружаем дополнительные изображения для галереи
    $gallery_paths = [];
    if (isset($_FILES['gallery']['name']) && count($_FILES['gallery']['name']) > 0) {
        // Обрабатываем каждый файл в галерее
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
    $query = "INSERT INTO directors (name, biography, photo, gallery) VALUES ('$name', '$biography', '$photo', '$gallery')";

    if (mysqli_query($conn, $query)) {
        // Сообщение об успешном добавлении
        $_SESSION['message'] = 'Директор успешно добавлен!';
        $_SESSION['message_type'] = 'success';  // Тип сообщения: успех
    } else {
        // Сообщение об ошибке SQL
        $_SESSION['message'] = 'Ошибка при добавлении директора: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';  // Тип сообщения: ошибка
    }

    // Перенаправление на страницу manage_directors.php
    header('Location: ../manage_directors.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить директора</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
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
        <h1 class="title mt-5">Добавить директора</h1>

        <!-- Выводим сообщение, если оно существует -->
        <?php if (isset($_SESSION['message'])): ?>
            <div id="message" class="notification is-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php
            // Очищаем сообщение после отображения 
            unset($_SESSION['message'], $_SESSION['message_type']);
            ?>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="field">
                <label class="label">Имя</label>
                <div class="control">
                    <input class="input" type="text" name="name" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Биография</label>
                <div class="control">
                    <textarea class="textarea" name="biography" required></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Фото</label>
                <div class="control">
                    <input class="input" type="file" name="photo" accept="image/*" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Галерея (несколько фото)</label>
                <div class="control">
                    <input class="input" type="file" name="gallery[]" accept="image/*" multiple>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <button class="button is-primary" type="submit">Добавить</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>