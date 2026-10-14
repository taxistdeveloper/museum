<?php
include '../config.php';

session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Завершаем выполнение скрипта
}

// Отображаем сообщение, если оно есть
if (isset($_SESSION['message'])):
?>
    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" id="message">
        <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
<?php endif;
$language = $_SESSION['lang']; // Получаем выбранный язык
// Получаем список всех директоров
$query = "SELECT * FROM directors";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление директорами</title>
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
                <h1>Директора</h1>
                <p>Имя и биография на странице руководства.</p>
            </div>
            <a class="btn btn-primary" href="directors/add_director.php">Добавить</a>
        </header>
        <?php if (mysqli_num_rows($result) == 0): ?>
            <div class="alert alert-warning">
                Нет доступных директоров.
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Имя</th>
                        <th>Биография</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo admin_preview($row['biography']); ?></td>
                            <td>
                                <a href="directors/edit_director.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Редактировать</a>
                                <a href="directors/delete_director.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Вы уверены, что хотите удалить этого директора?');">Удалить</a>
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