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
    <div class="notification is-<?php echo $_SESSION['message_type']; ?>" id="message">
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
        <h1 class="title has-text-centered mt-5">Управление директорами</h1>



        <div class="mb-4">
            <a href="directors/add_director.php" class="button is-primary">Добавить директора</a>
        </div>
        <div class="mb-4">
            <a href="../admin/index.php" class="button is-primary">назад</a>
        </div>
        <?php if (mysqli_num_rows($result) == 0): ?>
            <div class="notification is-warning">
                Нет доступных директоров.
            </div>
        <?php else: ?>
            <table class="table is-fullwidth">
                <thead>
                    <tr>
                        <th>Имя</th>
                        <th>Дата назначения</th>
                        <th>Описание</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($row['biography'])); ?></td>
                            <td>
                                <a href="directors/edit_director.php?id=<?php echo $row['id']; ?>" class="button is-small is-info">Редактировать</a>
                                <a href="directors/delete_director.php?id=<?php echo $row['id']; ?>" class="button is-small is-danger" onclick="return confirm('Вы уверены, что хотите удалить этого директора?');">Удалить</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>