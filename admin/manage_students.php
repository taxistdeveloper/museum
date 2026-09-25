<?php
session_start();
require '../config.php';

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Удаление студента
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $query_delete = "DELETE FROM students WHERE id = '$id'";
    if (mysqli_query($conn, $query_delete)) {
        echo "<h2 class='has-text-centered'>Студент удален успешно!</h2>";
    } else {
        echo "<h2 class='has-text-centered'>Ошибка удаления студента</h2>";
    }
}

// Получение списка студентов
$query_students = "SELECT * FROM students ORDER BY created_at DESC";
$result_students = mysqli_query($conn, $query_students);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление студентами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="h2 text-center mt-5">Управление студентами</h1>
        <!-- Кнопки навигации -->
        <div class="mb-4">
            <a href="../admin/students/add_student.php" class="btn btn-primary mb-5">Добавить студента</a>
            <a href="../admin/index.php" class="btn btn-light">Назад</a>
        </div>


        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Имя</th>

                    <th>Группа</th>

                    <th>Фото</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result_students) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result_students)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>

                            <td><?= htmlspecialchars($row['group_name']) ?></td>

                            <td>
                                <img src="./students/uploads/<?= htmlspecialchars($row['image']) ?>" alt="Фото" style="width: 50px; height: 50px;">
                            </td>
                            <td>
                                <a href="../admin/students/edit_student.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Редактировать</a>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этого студента?')">Удалить</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Нет студентов для отображения</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>