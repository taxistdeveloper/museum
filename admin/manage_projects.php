<?php
session_start(); // Начало сессии для управления сообщениями

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../config.php';

// Проверяем соединение с базой данных
if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

// Получаем список всех проектов
$query = "SELECT * FROM projects ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление проектами</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<script>
        // Скрипт для скрытия сообщения через 3 секунды
        window.onload = function() {
            const messageElement = document.getElementById('message');
            if (messageElement) {
                setTimeout(function() {
                    messageElement.style.display = 'none';
                }, 3000); // 3 секунды
            }
        }
    </script>
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/includes/topbar.php'; ?>

    <div class="container">
        <header class="page-head">
            <div>
                <a class="back-link" href="index.php">Разделы</a>
                <h1>Проекты</h1>
                <p>Название, описание и дата на сайте.</p>
            </div>
            <a class="btn btn-primary" href="projects/add_project.php">Добавить</a>
        </header>

        <!-- Сообщение -->
        <?php if (isset($_SESSION['message'])): ?>
            <div id="message" class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Таблица проектов -->
        <?php if (mysqli_num_rows($result) == 0): ?>
            <div class="alert alert-warning">
                Проекты не найдены.
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Дата создания</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo admin_preview($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="projects/edit_projects.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Редактировать</a>
                                <a href="projects/delete_projects.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этот проект?');">Удалить</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>