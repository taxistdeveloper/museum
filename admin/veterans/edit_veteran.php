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
$lang = $_GET['lang'] ?? 'ru';
if (!in_array($lang, ['ru', 'kz', 'en'], true)) {
    $lang = 'ru';
}

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
        $target_dir = __DIR__ . '/assets/images/';

        if (!is_dir($target_dir) && !mkdir($target_dir, 0775, true)) {
            $_SESSION['message'] = 'Не удалось создать папку для фото.';
            $_SESSION['message_type'] = 'error';
            header('Location: ?id=' . $veteran_id . '&lang=' . rawurlencode($lang));
            exit;
        }

        if ($_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
            $_SESSION['message'] = 'Ошибка загрузки: ' . $_FILES["photo"]["error"];
            $_SESSION['message_type'] = 'error';
            header('Location: ?id=' . $veteran_id . '&lang=' . rawurlencode($lang));
            exit;
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES["photo"]["tmp_name"]);
        $ext_map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        if (!isset($ext_map[$mime])) {
            $_SESSION['message'] = 'Недопустимый тип файла. Разрешены JPG, PNG, GIF, WEBP.';
            $_SESSION['message_type'] = 'error';
            header('Location: ?id=' . $veteran_id . '&lang=' . rawurlencode($lang));
            exit;
        }

        $photo = bin2hex(random_bytes(16)) . '.' . $ext_map[$mime];
        $target_file = $target_dir . $photo;

        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $reason = error_get_last()['message'] ?? 'нет прав на запись в assets/images';
            $_SESSION['message'] = 'Не удалось сохранить файл. ' . $reason;
            $_SESSION['message_type'] = 'error';
            header('Location: ?id=' . $veteran_id . '&lang=' . rawurlencode($lang));
            exit;
        }
    } else {
        // Если фото не загружено, используем старое значение
        $photo = $veteran['photo'];
    }

    // Список в админке читает name/biography, сайт — name_ru и остальные языки.
    if ($lang === 'ru') {
        $query = "UPDATE veterans SET name = ?, biography = ?, name_ru = ?, biography_ru = ?, photo = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $biography, $name, $biography, $photo, $veteran_id);
    } else {
        $query = "UPDATE veterans SET name_$lang = ?, biography_$lang = ?, photo = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $name, $biography, $photo, $veteran_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = 'Данные ветерана успешно обновлены!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Ошибка при обновлении данных: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    mysqli_stmt_close($stmt);

    header('Location: ?id=' . $veteran_id . '&lang=' . rawurlencode($lang));
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
    <link rel="stylesheet" href="../../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/../includes/topbar.php'; ?>

    <div class="container">
        <header class="page-head">
            <div>
                <a class="back-link" href="../manage_veterans.php">Ветераны</a>
                <h1>Редактировать: <?= htmlspecialchars($veteran['name_' . $lang] ?? '') ?></h1>
            </div>
        </header>

        <!-- Сообщение об ошибке или успехе -->
        <?php if (isset($_SESSION['message'])): ?>
            <div id="message" class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Переключатель языка -->
        <ul class="nav nav-tabs justify-content-center mb-3">
                <li class="nav-item"><a class="nav-link <?= $lang == 'ru' ? 'active' : '' ?>" href="?id=<?= $veteran['id'] ?>&lang=ru">Русский</a></li>
                <li class="nav-item"><a class="nav-link <?= $lang == 'kz' ? 'active' : '' ?>" href="?id=<?= $veteran['id'] ?>&lang=kz">Қазақша</a></li>
                <li class="nav-item"><a class="nav-link <?= $lang == 'en' ? 'active' : '' ?>" href="?id=<?= $veteran['id'] ?>&lang=en">English</a></li>
            </ul>

        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <div class="mb-3">
                <label class="form-label">Имя</label>
                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($veteran['name_' . $lang] ?? '') ?>" required>
                </div>

            <div class="mb-3">
                <label class="form-label">Биография</label>
                <textarea class="form-control" name="biography" required><?= htmlspecialchars($veteran['biography_' . $lang] ?? '') ?></textarea>
                </div>

            <div class="mb-3">
                <label class="form-label">Фото (оставьте пустым, если не меняете)</label>
                <input class="form-control" type="file" name="photo" accept="image/*">
                </div>

            <div class="mb-3">
                <button class="btn btn-primary" type="submit">Сохранить изменения</button>
                </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>