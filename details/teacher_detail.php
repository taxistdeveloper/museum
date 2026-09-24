<?php
include '../config.php';

// Получаем ID преподавателя из параметров URL
$teacher_id = $_GET['id'];

// Запрос на получение информации о преподавателе по ID
$query_teacher = "SELECT * FROM teachers WHERE id = '$teacher_id'";
$result_teacher = mysqli_query($conn, $query_teacher);
$teacher = mysqli_fetch_assoc($result_teacher);

// Если преподаватель не найден
if (!$teacher) {
    echo "<h2 class='has-text-centered'>Преподаватель не найден</h2>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Преподаватель: <?= htmlspecialchars($teacher['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .card-header {
            background-color: #3273dc;
            color: white;
            padding: 10px;
            border-radius: 10px;
        }

        .card-header h3 {
            margin: 0;
        }

        .card-content {
            margin-top: 20px;
        }

        .button {
            margin-top: 20px;
            width: 100%;
            font-size: 1.1rem;
            border-radius: 5px;
        }

        .button-back {
            background-color: #3273dc;
            color: white;
            transition: background-color 0.3s;
        }

        .button-back:hover {
            background-color: #276cda;
        }

        .subtitle {
            color: #4a4a4a;
        }

        p {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="title is-4"><?= htmlspecialchars($teacher['name']) ?></h3>
            </div>
            <div class="card-content">
                <h3 class="subtitle is-5">Описание</h3>
                <p><?= nl2br(htmlspecialchars($teacher['description'])) ?></p>
                <a href="../pages/teachers.php">
                    <button class="button button-back">Назад к списку преподавателей</button>
                </a>
            </div>
        </div>
    </div>
</body>

</html>