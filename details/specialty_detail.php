<?php
require '../config.php';

// Получаем ID специальности из параметров URL
$specialty_id = $_GET['id'];

// Запрос для получения информации о специальности по ID
$query_specialty = "SELECT * FROM specialties WHERE id = '$specialty_id'";
$result_specialty = mysqli_query($conn, $query_specialty);
$specialty = mysqli_fetch_assoc($result_specialty);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Специальность: <?= htmlspecialchars($specialty['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="title has-text-centered mt-5"><?= htmlspecialchars($specialty['name']) ?></h1>

        <div class="content">
            <p><strong>Описание:</strong></p>
            <p><?= nl2br(htmlspecialchars($specialty['description'])) ?></p>
        </div>

        <a href="../pages/specialties.php">
            <button class="button is-link">Назад</button>
        </a>
    </div>
</body>

</html>