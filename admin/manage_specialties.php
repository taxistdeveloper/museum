<?php
require '../config.php';

// Обработка удаления
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Подготовка и выполнение запроса на удаление
    $delete_query = "DELETE FROM specialties WHERE id = '$id'";

    if (mysqli_query($conn, $delete_query)) {
        header("Location: admin/manage_specialties.php");
        exit();
    } else {
        echo "Ошибка при удалении специальности: " . mysqli_error($conn);
    }
}

// Получение всех специальностей
$query = "SELECT * FROM specialties ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление специальностями</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="title has-text-centered mt-5">Управление специальностями</h1>
        <!-- Кнопки навигации -->
        <div class="mb-4">
            <a href="../admin/specality/add_speciality.php" class="button is-success mb-5">Добавить специальность</a>
            <a href="../admin/index.php" class="button is-light">Назад</a>
        </div>


        <table class="table is-fullwidth is-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($specialty = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $specialty['id'] ?></td>
                        <td><?= htmlspecialchars($specialty['name']) ?></td>
                        <td><?= htmlspecialchars($specialty['description']) ?></td>
                        <td>
                            <a href="specality/edit_specialty.php?id=<?= $specialty['id'] ?>" class="button is-small is-warning">Редактировать</a>
                            <!-- Кнопка для удаления -->
                            <a href="?delete_id=<?= $specialty['id'] ?>" class="button is-small is-danger" onclick="return confirm('Вы уверены, что хотите удалить эту специальность?')">Удалить</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>