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
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="title">Добавление истории колледжа</h1>
        <form action="add_history.php" method="POST">
            <div class="field">
                <label class="label">Название</label>
                <div class="control">
                    <input class="input" type="text" name="title" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Содержание</label>
                <div class="control">
                    <textarea class="textarea" name="content" required></textarea>
                </div>
            </div>
            <div class="control">
                <button class="button is-primary" type="submit">Добавить</button>
            </div>
        </form>
    </div>
</body>

</html>