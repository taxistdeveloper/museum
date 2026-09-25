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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <section class="py-4">
        <div class="container">
            <h1 class="h2">Редактировать видео</h1>

            <form method="POST" enctype="multipart/form-data" class="card card-body">
                <div class="mb-3">
                    <label class="form-label">Название</label>
                    <input class="form-control" name="title" required value="<?= htmlspecialchars($video['title']) ?>">
                    </div>

                <div class="mb-3">
                    <label class="form-label">Категория</label>
                    <input class="form-control" name="category" value="<?= htmlspecialchars($video['category']) ?>">
                    </div>

                <div class="mb-3">
                    <label class="form-label">Теги</label>
                    <input class="form-control" name="tags" value="<?= htmlspecialchars($video['tags']) ?>">
                    </div>

                <div class="mb-3">
                    <label class="form-label">YouTube-ссылка</label>
                    <input class="form-control" name="url" value="<?= htmlspecialchars($video['url']) ?>">
                    </div>

                <div class="mb-3">
                    <label class="form-label">Заменить видеофайл (необязательно)</label>
                    <input class="form-control" type="file" name="video_file" accept="video/*">
                    <?php if ($video['file_path']): ?>
                        <p class="mt-2">Текущее видео: <a href="<?= $video['file_path'] ?>" target="_blank">Смотреть</a></p>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-2 flex-wrap mt-4">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a href="manage_video.php" class="btn btn-light">Назад</a>
                    </div>
            </form>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>