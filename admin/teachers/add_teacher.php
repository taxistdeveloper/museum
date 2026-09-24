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
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
    <script>
        function addEducationField() {
            var container = document.getElementById("educationFields");
            var input = document.createElement("input");
            input.type = "text";
            input.name = "education[]";
            input.classList.add("input", "mt-2");
            container.appendChild(input);
        }
    </script>
</head>

<body>
    <div class="container">
        <h1 class="title has-text-centered mt-5">Добавить преподавателя</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="notification is-<?= $_SESSION['message_type']; ?>">
                <?= $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="tabs is-centered">
            <ul>
                <li class="<?= $lang == 'ru' ? 'is-active' : '' ?>"><a href="?lang=ru">Русский</a></li>
                <li class="<?= $lang == 'kz' ? 'is-active' : '' ?>"><a href="?lang=kz">Қазақша</a></li>
                <li class="<?= $lang == 'en' ? 'is-active' : '' ?>"><a href="?lang=en">English</a></li>
            </ul>
        </div>

        <form method="POST" enctype="multipart/form-data" class="box">
            <div class="field">
                <label class="label">Имя преподавателя</label>
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
                <label class="label">Образование</label>
                <div id="educationFields" class="control">
                    <input class="input" type="text" name="education[]" required>
                </div>
                <button type="button" class="button is-info mt-2" onclick="addEducationField()">Добавить образование</button>
            </div>

            <div class="field">
                <label class="label">Работает в колледже</label>
                <div class="control">
                    <input class="input" type="text" name="works_in_college" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Должность</label>
                <div class="control">
                    <input class="input" type="text" name="position" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Фото преподавателя</label>
                <div class="control">
                    <input class="input" type="file" name="image" accept="image/*">
                </div>
            </div>

            <div class="field">
                <div class="control">
                    <button class="button is-primary">Добавить</button>
                </div>
            </div>
        </form>

        <a href="../manage_teachers.php" class="button is-light mt-4">← Назад</a>
    </div>
</body>

</html>