<?php
session_start();
require '../../config.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Получаем язык
$lang = mysqli_real_escape_string($conn, $_GET['lang'] ?? 'ru');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $works_in_college = mysqli_real_escape_string($conn, $_POST['works_in_college']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $educations = json_encode($_POST['education'], JSON_UNESCAPED_UNICODE);

    // Обработка изображения
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }

    // Вставка в таблицу teachers
    $query_add_teacher = "INSERT INTO teachers (image, works_in_college, position, education, created_at) 
                          VALUES ('$image', '$works_in_college', '$position', '$educations', NOW())";
    if (!mysqli_query($conn, $query_add_teacher)) {
        die("Ошибка при добавлении teacher: " . mysqli_error($conn));
    }

    $teacher_id = mysqli_insert_id($conn);

    // Вставка перевода
    $query_add_lang = "INSERT INTO teachers_lang (teacher_id, language, name, description) 
                       VALUES ($teacher_id, '$lang', '$name', '$description')";
    if (!mysqli_query($conn, $query_add_lang)) {
        die("Ошибка при добавлении teachers_lang: " . mysqli_error($conn));
    }

    $_SESSION['message'] = 'Преподаватель добавлен!';
    $_SESSION['message_type'] = 'success';
    header("Location: add_teacher.php?lang=$lang");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Добавить преподавателя</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<script>
        function addEducationField() {
            var container = document.getElementById("educationFields");
            var input = document.createElement("input");
            input.type = "text";
            input.name = "education[]";
            input.classList.add("form-control", "mt-2");
            container.appendChild(input);
        }
    </script>
</head>

<body>
    <div class="container">
        <h1 class="h2 text-center mt-5">Добавить преподавателя</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type']; ?>">
                <?= $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <ul class="nav nav-tabs justify-content-center mb-3">
                <li class="nav-item"><a class="nav-link <?= $lang == 'ru' ? 'active' : '' ?>" href="?lang=ru">Русский</a></li>
                <li class="nav-item"><a class="nav-link <?= $lang == 'kz' ? 'active' : '' ?>" href="?lang=kz">Қазақша</a></li>
                <li class="nav-item"><a class="nav-link <?= $lang == 'en' ? 'active' : '' ?>" href="?lang=en">English</a></li>
            </ul>

        <form method="POST" enctype="multipart/form-data" class="card card-body">
            <div class="mb-3">
                <label class="form-label">Имя преподавателя</label>
                <input class="form-control" type="text" name="name" required>
                </div>

            <div class="mb-3">
                <label class="form-label">Описание</label>
                <textarea class="form-control" name="description" required></textarea>
                </div>

            <div class="mb-3">
                <label class="form-label">Образование</label>
                <div id="educationFields">
                    <input class="form-control" type="text" name="education[]" required>
                </div>
                <button type="button" class="btn btn-info mt-2" onclick="addEducationField()">Добавить образование</button>
            </div>

            <div class="mb-3">
                <label class="form-label">Работает в колледже</label>
                <input class="form-control" type="text" name="works_in_college" required>
                </div>

            <div class="mb-3">
                <label class="form-label">Должность</label>
                <input class="form-control" type="text" name="position" required>
                </div>

            <div class="mb-3">
                <label class="form-label">Фото преподавателя</label>
                <input class="form-control" type="file" name="image" accept="image/*">
                </div>

            <div class="mb-3">
                <button class="btn btn-primary">Добавить</button>
                </div>
        </form>

        <a href="../manage_teachers.php" class="btn btn-light mt-4">← Назад</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>