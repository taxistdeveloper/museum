<?php
include 'config.php';

// Получаем информацию о студенте по ID
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "SELECT * FROM students WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подробнее о студенте</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="title mt-5"><?= $student['name'] ?></h1>
        <div class="card">
            <div class="card-image">
                <figure class="image is-4by3">
                    <img src="assets/images/<?= $student['photo'] ?>" alt="Студент">
                </figure>
            </div>
            <div class="card-content">
                <p><strong>Биография:</strong></p>
                <p><?= nl2br($student['biography']) ?></p>
            </div>
        </div>
        <a href="students.php">
            <button class="button is-link">Назад</button>
        </a>
    </div>
</body>

</html>