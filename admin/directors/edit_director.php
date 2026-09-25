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

// Получаем ID директора из параметров URL
if (isset($_GET['id'])) {
    $director_id = $_GET['id'];

    // Запрос на получение информации о директоре по ID
    $query_director = "SELECT * FROM directors WHERE id = '$director_id'";
    $result_director = mysqli_query($conn, $query_director);
    $director = mysqli_fetch_assoc($result_director);
}

// Получение выбранного языка
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';  // По умолчанию русский

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // В зависимости от выбранного языка обновляем соответствующие поля
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $biography = mysqli_real_escape_string($conn, $_POST['biography']);

    // Обработка основного фото
    $photo = $_FILES['photo']['name'] ? $_FILES['photo']['name'] : $director['photo'];  // Если новое фото не выбрано, сохраняем старое
    $target_dir = "assets/images/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);

    if ($_FILES['photo']['name'] && !move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        echo "Ошибка при загрузке фото!<br>";
        exit;
    }

    // Обработка галереи
    $gallery_paths = [];
    if (isset($_FILES['gallery']['name']) && count($_FILES['gallery']['name']) > 0) {
        foreach ($_FILES['gallery']['name'] as $key => $file) {
            $gallery_path = $target_dir . basename($file);
            if (!move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $gallery_path)) {
                echo "Галерея: $file не загружено!<br>";
            } else {
                $gallery_paths[] = basename($file);  // Добавляем только имя файла
            }
        }
    }

    // Преобразуем массив путей галереи в строку
    $gallery = implode(",", $gallery_paths);

    // Обновляем информацию в базе данных в зависимости от выбранного языка
    if ($lang == 'ru') {
        $query = "UPDATE directors SET name_ru = '$name', biography_ru = '$biography', photo = '$photo', gallery = '$gallery' WHERE id = '$director_id'";
    } elseif ($lang == 'kz') {
        $query = "UPDATE directors SET name_kz = '$name', biography_kz = '$biography', photo = '$photo', gallery = '$gallery' WHERE id = '$director_id'";
    } else {
        $query = "UPDATE directors SET name_en = '$name', biography_en = '$biography', photo = '$photo', gallery = '$gallery' WHERE id = '$director_id'";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Данные директора успешно обновлены!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Ошибка при обновлении данных директора: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    // Перенаправление на страницу с директором
    header('Location: ?id=' . $director_id . '&lang=' . $lang);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать директора</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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
        <h1 class="h2 mt-5">Редактировать директора: <?= htmlspecialchars($director['name_ru'] ?? '') ?></h1>

        <!-- Сообщение об ошибке или успехе -->
        <?php if (isset($_SESSION['message'])): ?>
            <div id="message" class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Переключатель языка -->
        <ul class="nav nav-tabs justify-content-center mb-3">
                <li class="nav-item"><a class="nav-link <?= $lang == 'ru' ? 'active' : '' ?>" href="?id=<?= $director['id'] ?>&lang=ru">Русский</a></li>
                <li class="nav-item"><a class="nav-link <?= $lang == 'kz' ? 'active' : '' ?>" href="?id=<?= $director['id'] ?>&lang=kz">Қазақша</a></li>
                <li class="nav-item"><a class="nav-link <?= $lang == 'en' ? 'active' : '' ?>" href="?id=<?= $director['id'] ?>&lang=en">English</a></li>
            </ul>

        <form action="" method="POST" enctype="multipart/form-data">
            <!-- Имя и биография в зависимости от выбранного языка -->
            <div class="mb-3">
                <label class="form-label">Имя</label>
                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($director['name_' . $lang] ?? '') ?>" required>
                </div>

            <div class="mb-3">
                <label class="form-label">Биография</label>
                <textarea class="form-control" name="biography" required><?= htmlspecialchars($director['biography_' . $lang] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Фото -->
            <div class="mb-3">
                <label class="form-label">Фото</label>
                <input class="form-control" type="file" name="photo" accept="image/*">
                    <p>Текущее фото: <img src="assets/images/<?= htmlspecialchars($director['photo'] ?? '') ?>" alt="Фото директора" width="100"></p>
                </div>
            </div>

            <!-- Галерея -->
            <div class="mb-3">
                <label class="form-label">Галерея (несколько фото)</label>
                <input class="form-control" type="file" name="gallery[]" accept="image/*" multiple>
                    <p>Текущая галерея:</p>
                    <?php if (!empty($director['gallery'])): ?>
                        <?php foreach (explode(',', $director['gallery']) as $gallery_image): ?>
                            <img src="assets/images/<?= htmlspecialchars($gallery_image) ?>" alt="Gallery Image" width="100" style="margin-right: 10px;">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            <div class="mb-3">
                <button class="btn btn-primary" type="submit">Сохранить изменения</button>
                </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>