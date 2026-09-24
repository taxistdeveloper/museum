<?php
require '../../config.php';

// Проверка на ID записи
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "SELECT * FROM history WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
}

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $update_query = "UPDATE history SET title = '$title', content = '$content' WHERE id = $id";
    if (mysqli_query($conn, $update_query)) {
        header("Location: ././pages/history.php?message=success");
        exit;
    } else {
        echo "Ошибка: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование истории</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="title">Редактирование истории</h1>
        <form action="edit_history.php?id=<?= $id ?>" method="POST">
            <div class="field">
                <label class="label">Название</label>
                <div class="control">
                    <input class="input" type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Содержание</label>
                <div class="control">
                    <textarea class="textarea" name="content" required><?= htmlspecialchars($row['content']) ?></textarea>
                </div>
            </div>
            <div class="control">
                <button class="button is-primary" type="submit">Обновить</button>
            </div>
        </form>
    </div>
</body>

</html>