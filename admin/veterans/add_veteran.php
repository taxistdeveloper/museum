<?php
session_start();  // Начинаем сессию для передачи сообщений

require '../../config.php';

// Проверяем соединение с базой данных
if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $biography = mysqli_real_escape_string($conn, $_POST['biography']);
    $photo = $_FILES['photo']['name'];
    $target_dir = "assets/images/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);

    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = 'Ошибка загрузки файла: ' . $_FILES['photo']['error'];
        $_SESSION['message_type'] = 'error';
    } elseif ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
        $_SESSION['message'] = 'Файл слишком большой. Максимальный размер: 5 MB.';
        $_SESSION['message_type'] = 'error';
    } elseif (!in_array($_FILES['photo']['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
        $_SESSION['message'] = 'Недопустимый тип файла. Разрешены только JPG, PNG, GIF.';
        $_SESSION['message_type'] = 'error';
    } elseif (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        $_SESSION['message'] = 'Не удалось сохранить файл в: ' . $target_file;
        $_SESSION['message_type'] = 'error';
    } else {
        // Если всё прошло успешно, добавляем запись в базу
        $query = "INSERT INTO veterans (name, biography, photo) VALUES ('$name', '$biography', '$photo')";
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = 'Ветеран добавлен успешно!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Ошибка при добавлении ветерана: ' . mysqli_error($conn);
            $_SESSION['message_type'] = 'error';
        }
    }

    header('Location: admin/manage_veterans.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить ветерана</title>
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
        <h1 class="h2 mt-5">Добавить ветерана</h1>

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
                <label class="form-label">Имя</label>
                <input class="form-control" type="text" name="name" required>
                </div>

            <div class="mb-3">
                <label class="form-label">Биография</label>
                <textarea class="form-control" name="biography" required></textarea>
                </div>

            <div class="mb-3">
                <label class="form-label">Фото</label>
                <input class="form-control" type="file" name="photo" accept="image/*" required>
                </div>

            <div class="mb-3">
                <button class="btn btn-primary" type="submit">Добавить</button>
                </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>