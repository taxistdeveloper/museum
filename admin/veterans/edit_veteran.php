<?php
session_start();
require '../../config.php';

if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

// Получаем ID ветерана
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $veteran_id = intval($_GET['id']);
} else {
    die("Некорректный ID ветерана.");
}

// Получение выбранного языка
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru'; // По умолчанию русский

// Подготовленный запрос на получение информации о ветеране
$query_veteran = "SELECT * FROM veterans WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_veteran);
mysqli_stmt_bind_param($stmt, "i", $veteran_id);
mysqli_stmt_execute($stmt);
$result_veteran = mysqli_stmt_get_result($stmt);
$veteran = mysqli_fetch_assoc($result_veteran);
mysqli_stmt_close($stmt);

if (!$veteran) {
    die("Ветеран не найден.");
}

// Обновление данных при отправке формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $biography = trim(mysqli_real_escape_string($conn, $_POST['biography']));

    // CSRF-защита
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ошибка безопасности. Попробуйте еще раз.");
    }

    // Если новое фото загружено
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "assets/images/";

        // Создаем папку, если её нет
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0775, true);
        }

        // Генерируем уникальное имя файла
        $photo = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $photo;

        // Проверяем ошибки загрузки
        if ($_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
            $_SESSION['message'] = 'Ошибка загрузки: ' . $_FILES["photo"]["error"];
            $_SESSION['message_type'] = 'error';
            header('Location: ' . $_SERVER['PHP_SELF'] . "?id=$veteran_id&lang=$lang");
            exit;
        }

        // Перемещаем файл в папку
        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $_SESSION['message'] = 'Ошибка при сохранении файла!';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . $_SERVER['PHP_SELF'] . "?id=$veteran_id&lang=$lang");
            exit;
        }
    } else {
        // Если фото не загружено, используем старое значение
        $photo = $veteran['photo'];
    }

    // Обновляем информацию в базе данных
    $query = "UPDATE veterans SET name_$lang = ?, biography_$lang = ?, photo = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $biography, $photo, $veteran_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = 'Данные ветерана успешно обновлены!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Ошибка при обновлении данных: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    mysqli_stmt_close($stmt);

    header('Location: ?id=' . $veteran_id . '&lang=' . $lang);
    exit;


    // Обновляем информацию в базе данных
    $query = "UPDATE veterans SET name_$lang = ?, biography_$lang = ?, photo = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssi", $name, $biography, $photo, $veteran_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = 'Данные ветерана успешно обновлены!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Ошибка при обновлении данных: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    mysqli_stmt_close($stmt);

    header('Location: ?id=' . $veteran_id . '&lang=' . $lang);
    exit;
}

// Генерация CSRF-токена
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать ветерана</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
    <script>
        window.onload = function() {
            const messageElement = document.getElementById('message');
            if (messageElement) {
                setTimeout(function() {
                    messageElement.style.display = 'none';
                }, 2000); // 2 секунды
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <h1 class="title mt-5">Редактировать ветерана <?= htmlspecialchars($veteran['name_' . $lang] ?? '') ?></h1>

        <!-- Сообщение об ошибке или успехе -->
        <?php if (isset($_SESSION['message'])): ?>
            <div id="message" class="notification is-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Переключатель языка -->
        <div class="tabs is-centered">
            <ul>
                <li class="<?= $lang == 'ru' ? 'is-active' : '' ?>"><a href="?id=<?= $veteran['id'] ?>&lang=ru">Русский</a></li>
                <li class="<?= $lang == 'kz' ? 'is-active' : '' ?>"><a href="?id=<?= $veteran['id'] ?>&lang=kz">Қазақша</a></li>
                <li class="<?= $lang == 'en' ? 'is-active' : '' ?>"><a href="?id=<?= $veteran['id'] ?>&lang=en">English</a></li>
            </ul>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <div class="field">
                <label class="label">Имя</label>
                <div class="control">
                    <input class="input" type="text" name="name" value="<?= htmlspecialchars($veteran['name_' . $lang] ?? '') ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Биография</label>
                <div class="control">
                    <textarea class="textarea" name="biography" required><?= htmlspecialchars($veteran['biography_' . $lang] ?? '') ?></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Фото (оставьте пустым, если не меняете)</label>
                <div class="control">
                    <input class="input" type="file" name="photo" accept="image/*">
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <button class="button is-primary" type="submit">Сохранить изменения</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>