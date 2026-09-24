<?php
session_start();
require '../config.php';

$upload_dir = __DIR__ . '/uploads/videos/';
$public_upload_dir = '/admin/uploads/videos/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    $_SESSION['message'] = 'Неверный ID видео.';
    header('Location: manage_video.php');
    exit;
}

// Получаем текущее видео
$stmt = $conn->prepare("SELECT * FROM videos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$video = $result->fetch_assoc();
$stmt->close();

if (!$video) {
    $_SESSION['message'] = 'Видео не найдено.';
    header('Location: manage_video.php');
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $tags = mysqli_real_escape_string($conn, $_POST['tags']);
    $url = !empty($_POST['url']) ? mysqli_real_escape_string($conn, $_POST['url']) : null;
    $file_path = $video['file_path'];

    // Если загружен новый файл — заменяем
    if (!empty($_FILES['video_file']['name'])) {
        // Удаляем старый файл
        if ($file_path && file_exists(__DIR__ . $file_path)) {
            unlink(__DIR__ . $file_path);
        }

        $file_name = time() . '_' . preg_replace('/\s+/', '_', basename($_FILES['video_file']['name']));
        $target_file = $upload_dir . $file_name;
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['mp4', 'mov', 'avi', 'mkv'];
        if (in_array($ext, $allowed) && move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
            $file_path = $public_upload_dir . $file_name;
        } else {
            $_SESSION['message'] = 'Ошибка загрузки файла.';
            header("Location: edit_video.php?id=$id");
            exit;
        }
    }

    $stmt = $conn->prepare("UPDATE videos SET title=?, category=?, tags=?, url=?, file_path=? WHERE id=?");
    $stmt->bind_param("sssssi", $title, $category, $tags, $url, $file_path, $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = 'Видео успешно обновлено!';
    header("Location: manage_video.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактировать видео</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
</head>

<body>
    <section class="section">
        <div class="container">
            <h1 class="title">Редактировать видео</h1>

            <form method="POST" enctype="multipart/form-data" class="box">
                <div class="field">
                    <label class="label">Название</label>
                    <div class="control">
                        <input class="input" name="title" required value="<?= htmlspecialchars($video['title']) ?>">
                    </div>
                </div>

                <div class="field">
                    <label class="label">Категория</label>
                    <div class="control">
                        <input class="input" name="category" value="<?= htmlspecialchars($video['category']) ?>">
                    </div>
                </div>

                <div class="field">
                    <label class="label">Теги</label>
                    <div class="control">
                        <input class="input" name="tags" value="<?= htmlspecialchars($video['tags']) ?>">
                    </div>
                </div>

                <div class="field">
                    <label class="label">YouTube-ссылка</label>
                    <div class="control">
                        <input class="input" name="url" value="<?= htmlspecialchars($video['url']) ?>">
                    </div>
                </div>

                <div class="field">
                    <label class="label">Заменить видеофайл (необязательно)</label>
                    <div class="control">
                        <input type="file" name="video_file" accept="video/*">
                    </div>
                    <?php if ($video['file_path']): ?>
                        <p class="mt-2">Текущее видео: <a href="<?= $video['file_path'] ?>" target="_blank">Смотреть</a></p>
                    <?php endif; ?>
                </div>

                <div class="field is-grouped mt-4">
                    <div class="control">
                        <button type="submit" class="button is-primary">Сохранить</button>
                    </div>
                    <div class="control">
                        <a href="manage_video.php" class="button is-light">Назад</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</body>

</html>