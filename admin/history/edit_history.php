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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="h2">Редактирование истории</h1>
        <form action="edit_history.php?id=<?= $id ?>" method="POST">
            <div class="mb-3">
                <label class="form-label">Название</label>
                <input class="form-control" type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>" required>
                </div>
            <div class="mb-3">
                <label class="form-label">Содержание</label>
                <textarea class="form-control" name="content" required><?= htmlspecialchars($row['content']) ?></textarea>
                </div>
            <button class="btn btn-primary" type="submit">Обновить</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>