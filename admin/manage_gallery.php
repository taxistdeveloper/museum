<?php

session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Завершаем выполнение скрипта
}

require '../config.php';
require_once __DIR__ . '/../includes/uploads.php';

// Запрос для получения всех изображений
$query_gallery = "SELECT * FROM gallery ORDER BY created_at DESC";
$result_gallery = mysqli_query($conn, $query_gallery);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление галереей</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/includes/topbar.php'; ?>

    <div class="container gallery-container">
        <header class="page-head">
            <div>
                <a class="back-link" href="index.php">Разделы</a>
                <h1>Фото</h1>
                <p>Снимки галереи и короткая подпись.</p>
            </div>
            <a class="btn btn-primary" href="gallery/add_gallery.php">Добавить</a>
        </header>


        <div class="row g-3">
            <?php while ($row = mysqli_fetch_assoc($result_gallery)) { ?>
                <div class="col-md-3 gallery-item">
                    <div class="card">
                        <div class="card-image">
                            <img src="<?= htmlspecialchars(museum_upload_src('gallery', (string)$row['file_name'])) ?>" alt="Фото" class="gallery-image">
                        </div>
                        <div class="card-body">
                            <p class="text-muted"><?= htmlspecialchars(htmlspecialchars_decode((string) $row['description'])) ?></p>
                            <div class="d-flex justify-content-center gap-2 flex-wrap mt-3">
                                <a href="../admin/gallery/edit_gallery.php?id=<?= $row['id'] ?>" class="btn btn-info">Редактировать</a>
                                <a href="../admin/gallery/delete_gallery.php?id=<?= $row['id'] ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Вы уверены, что хотите удалить изображение?')">Удалить</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>