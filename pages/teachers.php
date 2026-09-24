<?php
include '../config.php';

// Запрос для получения всех преподавателей
$query_teachers = "SELECT * FROM teachers ORDER BY created_at DESC";
$result_teachers = mysqli_query($conn, $query_teachers);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Преподаватели</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h1 class="title has-text-centered mt-5">Преподаватели колледжа</h1>

        <div class="columns is-multiline mt-5">
            <?php while ($teacher = mysqli_fetch_assoc($result_teachers)): ?>
                <div class="column is-one-quarter">
                    <div class="card">
                        <div class="card-content">
                            <h3 class="title is-5"><?= htmlspecialchars($teacher['name']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($teacher['description'])) ?></p>
                            <a href="../details/teacher_detail.php?id=<?= $teacher['id'] ?>" class="button is-primary">Подробнее</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <a href="../index.php">
            <button class="button is-link">Назад</button>
        </a>
    </div>
</body>

</html>