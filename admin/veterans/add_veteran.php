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
        <h1 class="title mt-5">Добавить ветерана</h1>

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
                <div class="control">
                    <button class="button is-primary" type="submit">Добавить</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>