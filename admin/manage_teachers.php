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
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/includes/topbar.php'; ?>

    <div class="container">
        <header class="page-head">
            <div>
                <a class="back-link" href="index.php">Разделы</a>
                <h1>Преподаватели</h1>
                <p>Имя, описание и фото на странице колледжа.</p>
            </div>
            <a class="btn btn-primary" href="teachers/add_teacher.php">Добавить</a>
        </header>

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
                        <td><?= admin_preview($teacher['description']) ?></td>
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