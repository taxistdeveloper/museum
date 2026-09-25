<?php
session_start(); // Начало сессии для управления сообщениями

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../config.php';
// Запрос для получения всех преподавателей
$query_teachers = "SELECT * FROM teachers ORDER BY created_at DESC";
$result_teachers = mysqli_query($conn, $query_teachers);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Преподаватели</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
        .teacher-photo {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            /* Круглая форма изображения */
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="h2 text-center mt-5">Список преподавателей</h1>
        <!-- Кнопки навигации -->
        <div class="mb-4">
            <a href="teachers/add_teacher.php">
                <button class="btn btn-primary">Добавить преподавателя</button>
            </a>
            <a href="../admin/index.php" class="btn btn-light">Назад</a>
        </div>

        <!-- Таблица преподавателей -->
        <table class="table table-striped table-hover mt-5">
            <thead>
                <tr>
                    <th>Фото</th>
                    <th>Имя</th>
                    <th>Описание</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($teacher = mysqli_fetch_assoc($result_teachers)): ?>
                    <tr>
                        <!-- Отображение фотографии -->
                        <td>
                            <img src="./teachers/uploads/<?= htmlspecialchars($teacher['image']) ?>" alt="Фото" style="width: 50px; height: 50px;">
                        </td>
                        <td><?= htmlspecialchars($teacher['name']) ?></td>
                        <td><?= nl2br(htmlspecialchars($teacher['description'])) ?></td>
                        <td>
                            <a href="teachers/edit_teacher.php?id=<?= $teacher['id'] ?>" class="btn btn-warning">Редактировать</a>
                            <a href="teachers/delete_teacher.php?id=<?= $teacher['id'] ?>" class="btn btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этого преподавателя?')">Удалить</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>