<?php

session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Завершаем выполнение скрипта
}

require '../config.php';

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
<style>
        body {
            background-color: #f5f5f5;
        }

        .gallery-container {
            margin-top: 30px;
        }

        .gallery-item {
            margin-bottom: 30px;
        }

        .card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card-content {
            background: #fff;
            padding: 15px;
        }
    </style>
</head>

<body>
    <div class="container gallery-container">
        <h1 class="h2 text-center">Управление галереей</h1>
        <!-- Кнопки навигации -->
        <div class="mb-4">
            <a href="./gallery/add_gallery.php" class="btn btn-primary mb-5">Добавить изображение</a>
            <a href="../admin/index.php" class="btn btn-light">Назад</a>
        </div>


        <div class="row g-3">
            <?php while ($row = mysqli_fetch_assoc($result_gallery)) { ?>
                <div class="col-md-3 gallery-item">
                    <div class="card">
                        <div class="card-image">
                            <img src="../admin/gallery/uploads/<?= htmlspecialchars($row['file_name']) ?>" alt="Фото" class="gallery-image">
                        </div>
                        <div class="card-body">
                            <p class="text-muted text-center"><?= htmlspecialchars($row['description']) ?></p>
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