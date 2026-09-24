<?php
require '../../config.php';

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $query = "INSERT INTO specialties (name, description) VALUES ('$name', '$description')";
    mysqli_query($conn, $query);

    header('Location: admin/manage_specialties.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить специальность</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="title has-text-centered mt-5">Добавить специальность</h1>
        <form method="POST">
            <div class="field">
                <label class="label">Название</label>
                <div class="control">
                    <input class="input" type="text" name="name" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Описание</label>
                <div class="control">
                    <textarea class="textarea" name="description" required></textarea>
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <button class="button is-primary" type="submit">Добавить</button>
                    <a href="../manage_specialties.php" class="button is-link">Отмена</a>
                </div>
            </div>
        </form>
    </div>
</body>

</html>