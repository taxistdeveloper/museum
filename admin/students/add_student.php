<?php
session_start();
require '../../config.php';

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $group = mysqli_real_escape_string($conn, $_POST['group']);


    // Обработка дополнительной информации (массив)
    $achievements = $_POST['achievements'];
    $achievements_json = json_encode($achievements, JSON_UNESCAPED_UNICODE);  // Преобразуем массив в строку JSON

    // Обработка изображения
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = __DIR__ . '/uploads/' . $image;

    // Перемещаем файл в папку для загрузки
    if (!move_uploaded_file($image_tmp, $image_path)) {
        echo "<h2 class='has-text-centered'>Ошибка загрузки файла</h2>";
        exit;
    }

    // Запрос для добавления нового студента
    $query_add_student = "INSERT INTO students (name,  group_name,  achievements, image, created_at) 
                          VALUES ('$name',  '$group',  '$achievements_json', '$image', NOW())";

    if (mysqli_query($conn, $query_add_student)) {
        echo "<h2 class='has-text-centered'>Студент добавлен успешно!</h2>";
        echo "<a href='../manage_students.php' class='btn btn-primary'>Перейти к списку студентов</a>";
    } else {
        echo "<h2 class='has-text-centered'>Ошибка добавления студента</h2>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить студента</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<script>
        // Функция для добавления новых полей достижений
        function addAchievementField() {
            var container = document.getElementById("achievementFields");
            var inputField = document.createElement("input");
            inputField.classList.add("form-control");
            inputField.classList.add("mt-2");
            inputField.setAttribute("type", "text");
            inputField.setAttribute("name", "achievements[]");
            container.appendChild(inputField);
        }
    </script>
</head>

<body>
    <div class="container">
        <h1 class="h2 text-center mt-5">Добавить студента</h1>

        <form action="add_student.php" method="POST" enctype="multipart/form-data" class="card card-body">
            <div class="mb-3">
                <label class="form-label">Имя студента</label>
                <input class="form-control" type="text" name="name" required>
                </div>



            <div class="mb-3">
                <label class="form-label">Группа</label>
                <input class="form-control" type="text" name="group" required>
                </div>



            <div class="mb-3">
                <label class="form-label">Достижения</label>
                <div id="achievementFields">
                    <input class="form-control" type="text" name="achievements[]">
                </div>
                <button type="button" class="btn btn-info mt-2" onclick="addAchievementField()">Добавить достижение</button>
            </div>

            <div class="mb-3">
                <label class="form-label">Фото студента</label>
                <input class="form-control" type="file" name="image" accept="image/*" required>
                </div>

            <div class="mb-3">
                <button class="btn btn-primary">Добавить</button>
                </div>
        </form>

        <a href="../manage_students.php">
            <button class="btn btn-primary">Назад</button>
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>