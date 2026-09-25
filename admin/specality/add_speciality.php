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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="h2 text-center mt-5">Добавить специальность</h1>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Название</label>
                <input class="form-control" type="text" name="name" required>
                </div>

            <div class="mb-3">
                <label class="form-label">Описание</label>
                <textarea class="form-control" name="description" required></textarea>
                </div>

            <div class="mb-3">
                <button class="btn btn-primary" type="submit">Добавить</button>
                    <a href="../manage_specialties.php" class="btn btn-primary">Отмена</a>
                </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>