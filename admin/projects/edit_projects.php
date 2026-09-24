<?php
session_start();
require '../../config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$lang = $_GET['lang'] ?? 'ru';

// Получаем проект
$query = "SELECT * FROM projects WHERE id = $project_id";
$result = mysqli_query($conn, $query);
$project = mysqli_fetch_assoc($result);
if (!$project) {
    $_SESSION['message'] = 'Проект не найден!';
    $_SESSION['message_type'] = 'warning';
    header('Location: admin/manage_projects.php');
    exit();
}

// Получаем перевод
$query_lang = "SELECT * FROM projects_lang WHERE project_id = $project_id AND language = '$lang'";
$result_lang = mysqli_query($conn, $query_lang);
$project_lang = mysqli_fetch_assoc($result_lang);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = $_POST['description']; // Сохраняем как HTML

    // Обработка фото
    $photo = $project['photo'];
    $target_dir = "assets/images/";
    if (!empty($_FILES['photo']['name'])) {
        $clean_name = str_replace(' ', '_', basename($_FILES['photo']['name']));
        $target_file = $target_dir . $clean_name;
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $photo = $clean_name;
        } else {
            echo "Ошибка при загрузке фото!";
            exit;
        }
    }

    // Обработка галереи
    $gallery = $project['gallery'];
    $gallery_paths = [];

    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['name'] as $key => $file) {
            $clean_name = str_replace(' ', '_', basename($file));
            $gallery_path = $target_dir . $clean_name;
            if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $gallery_path)) {
                $gallery_paths[] = $clean_name;
            }
        }
        $gallery = implode(",", $gallery_paths);
    }

    // Обновление проекта
    $query_update = "UPDATE projects SET photo = '$photo', gallery = '$gallery' WHERE id = $project_id";
    mysqli_query($conn, $query_update);

    // Перевод
    $check = mysqli_query($conn, "SELECT id FROM projects_lang WHERE project_id = $project_id AND language = '$lang'");
    if (mysqli_fetch_assoc($check)) {
        $query_lang = "UPDATE projects_lang SET name = '$name', description = '$description' WHERE project_id = $project_id AND language = '$lang'";
    } else {
        $query_lang = "INSERT INTO projects_lang (project_id, language, name, description) VALUES ($project_id, '$lang', '$name', '$description')";
    }
    mysqli_query($conn, $query_lang);

    $_SESSION['message'] = 'Проект успешно обновлен!';
    $_SESSION['message_type'] = 'success';
    header("Location: ?id=$project_id&lang=$lang");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактировать проект</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
    <style>
        img.gallery-thumb {
            width: 100px;
            margin: 5px;
            border-radius: 6px;
            object-fit: cover;
        }
    </style>

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/4.22.1/standard-all/ckeditor.js"></script>
    <script>
        window.onload = function() {
            CKEDITOR.replace('description', {
                extraPlugins: 'table',
                height: 400
            });

            const msg = document.getElementById('message');
            if (msg) setTimeout(() => msg.style.display = 'none', 3000);
        };
    </script>
</head>

<body>
    <div class="container">
        <h1 class="title mt-5">Редактировать проект</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div id="message" class="notification is-<?= $_SESSION['message_type'] ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="tabs is-centered">
            <ul>
                <li class="<?= $lang == 'ru' ? 'is-active' : '' ?>"><a href="?id=<?= $project_id ?>&lang=ru">Русский</a></li>
                <li class="<?= $lang == 'kz' ? 'is-active' : '' ?>"><a href="?id=<?= $project_id ?>&lang=kz">Қазақша</a></li>
                <li class="<?= $lang == 'en' ? 'is-active' : '' ?>"><a href="?id=<?= $project_id ?>&lang=en">English</a></li>
            </ul>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="field">
                <label class="label">Название</label>
                <div class="control">
                    <input class="input" type="text" name="name" value="<?= htmlspecialchars($project_lang['name'] ?? '') ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Описание (поддерживает таблицы)</label>
                <div class="control">
                    <textarea name="description" id="description"><?= $project_lang['description'] ?? '' ?></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Главное фото</label>
                <div class="control">
                    <input class="input" type="file" name="photo" accept="image/*">
                    <?php if (!empty($project['photo'])): ?>
                        <p>Текущее фото: <br><img src="assets/images/<?= htmlspecialchars($project['photo']) ?>" width="120"></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="field">
                <label class="label">Галерея (несколько фото)</label>
                <div class="control">
                    <input class="input" type="file" name="gallery[]" accept="image/*" multiple>
                    <?php if (!empty($project['gallery'])): ?>
                        <p>Текущая галерея:</p>
                        <?php foreach (explode(',', $project['gallery']) as $img): ?>
                            <img class="gallery-thumb" src="assets/images/<?= htmlspecialchars($img) ?>" alt="Gallery Image">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="field mt-4">
                <div class="control">
                    <button class="button is-primary" type="submit">Сохранить изменения</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>