<?php
require '../../config.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);  // Защита от SQL инъекций
    $content = mysqli_real_escape_string($conn, $_POST['content']);  // Защита от SQL инъекций

    // Вставляем данные в таблицу history
    $query = "INSERT INTO history (title, content) VALUES ('$title', '$content')";
    if (mysqli_query($conn, $query)) {
        // Перенаправляем на страницу history.php после успешного добавления
        header("Location: ././pages/history.php");
        exit();  // Останавливаем выполнение скрипта
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
    <title>Добавление истории колледжа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="h2">Добавление истории колледжа</h1>
        <form action="add_history.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Название</label>
                <input class="form-control" type="text" name="title" required>
                </div>
            <div class="mb-3">
                <label class="form-label">Содержание</label>
                <textarea class="form-control" name="content" required></textarea>
                </div>
            <button class="btn btn-primary" type="submit">Добавить</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>