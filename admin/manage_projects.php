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
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
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
</head>

<body>
    <div class="container">
        <h1 class="title has-text-centered mt-5">Управление проектами</h1>

        <!-- Кнопки навигации -->
        <div class="mb-4">
            <a href="projects/add_project.php" class="button is-primary">Добавить проект</a>
            <a href="../admin/index.php" class="button is-light">Назад</a>
        </div>

        <!-- Сообщение -->
        <?php if (isset($_SESSION['message'])): ?>
            <div id="message" class="notification is-<?php echo $_SESSION['message_type']; ?>">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Таблица проектов -->
        <?php if (mysqli_num_rows($result) == 0): ?>
            <div class="notification is-warning">
                Проекты не найдены.
            </div>
        <?php else: ?>
            <table class="table is-fullwidth">
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
                            <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="projects/edit_projects.php?id=<?php echo $row['id']; ?>" class="button is-small is-info">Редактировать</a>
                                <a href="projects/delete_projects.php?id=<?php echo $row['id']; ?>" class="button is-small is-danger" onclick="return confirm('Вы уверены, что хотите удалить этот проект?');">Удалить</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>