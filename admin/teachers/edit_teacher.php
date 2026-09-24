<?php
session_start();
require '../../config.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Получение ID преподавателя
if (!isset($_GET['id'])) {
    echo "<h2 class='has-text-centered'>ID преподавателя не передан</h2>";
    exit();
}

$teacher_id = intval($_GET['id']);
$lang = mysqli_real_escape_string($conn, $_GET['lang'] ?? 'ru');

// Получаем основной профиль преподавателя
$query_teacher = "SELECT * FROM teachers WHERE id = $teacher_id";
$result_teacher = mysqli_query($conn, $query_teacher);
$teacher = mysqli_fetch_assoc($result_teacher);

if (!$teacher) {
    echo "<h2 class='has-text-centered'>Преподаватель не найден</h2>";
    exit();
}

// Получаем перевод
$query_lang = "SELECT * FROM teachers_lang WHERE teacher_id = $teacher_id AND language = '$lang'";
$result_lang = mysqli_query($conn, $query_lang);
$teacher_lang = mysqli_fetch_assoc($result_lang);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $works_in_college = mysqli_real_escape_string($conn, $_POST['works_in_college']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $educations = json_encode($_POST['education'], JSON_UNESCAPED_UNICODE);

    // Обработка фото
    $image = $teacher['image'];
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    }

    // Обновляем общие данные
    $query_update_teacher = "UPDATE teachers SET 
                             image = '$image', 
                             works_in_college = '$works_in_college', 
                             position = '$position', 
                             education = '$educations',
                             updated_at = NOW() 
                             WHERE id = $teacher_id";
    if (!mysqli_query($conn, $query_update_teacher)) {
        die("Ошибка при обновлении teacher: " . mysqli_error($conn));
    }

    // Проверяем наличие перевода
    $query_check = "SELECT id FROM teachers_lang WHERE teacher_id = $teacher_id AND language = '$lang'";
    $result_check = mysqli_query($conn, $query_check);
    $exists = mysqli_fetch_assoc($result_check);

    if ($exists) {
        $query_update_lang = "UPDATE teachers_lang SET 
                              name = '$name', 
                              description = '$description' 
                              WHERE teacher_id = $teacher_id AND language = '$lang'";
    } else {
        $query_update_lang = "INSERT INTO teachers_lang (teacher_id, language, name, description) 
                              VALUES ($teacher_id, '$lang', '$name', '$description')";
    }

    if (!mysqli_query($conn, $query_update_lang)) {
        die("Ошибка при обновлении teachers_lang: " . mysqli_error($conn));
    }

    $_SESSION['message'] = 'Данные преподавателя обновлены!';
    $_SESSION['message_type'] = 'success';

    header("Location: ?id=$teacher_id&lang=$lang");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактировать преподавателя</title>
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
        <h1 class="title has-text-centered mt-5">Редактировать преподавателя</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="notification is-<?= $_SESSION['message_type']; ?>">
                <?= $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="tabs is-centered">
            <ul>
                <li class="<?= $lang == 'ru' ? 'is-active' : '' ?>"><a href="?id=<?= $teacher_id ?>&lang=ru">Русский</a></li>
                <li class="<?= $lang == 'kz' ? 'is-active' : '' ?>"><a href="?id=<?= $teacher_id ?>&lang=kz">Қазақша</a></li>
                <li class="<?= $lang == 'en' ? 'is-active' : '' ?>"><a href="?id=<?= $teacher_id ?>&lang=en">English</a></li>
            </ul>
        </div>

        <form method="POST" enctype="multipart/form-data" class="box">
            <div class="field">
                <label class="label">Имя преподавателя</label>
                <div class="control">
                    <input class="input" type="text" name="name" value="<?= htmlspecialchars($teacher_lang['name'] ?? '') ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Описание</label>
                <div class="control">
                    <textarea class="textarea" name="description" required><?= htmlspecialchars($teacher_lang['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="field">
                <label class="label">Образование</label>
                <div id="educationFields" class="control">
                    <?php
                    $educations = json_decode($teacher['education'], true) ?? [];
                    foreach ($educations as $education): ?>
                        <input class="input mt-2" type="text" name="education[]" value="<?= htmlspecialchars($education) ?>" required>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="button is-info mt-2" onclick="addEducationField()">Добавить образование</button>
            </div>

            <div class="field">
                <label class="label">Работает в колледже</label>
                <div class="control">
                    <input class="input" type="text" name="works_in_college" value="<?= htmlspecialchars($teacher['works_in_college']) ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Должность</label>
                <div class="control">
                    <input class="input" type="text" name="position" value="<?= htmlspecialchars($teacher['position']) ?>" required>
                </div>
            </div>

            <div class="field">
                <label class="label">Фото преподавателя</label>
                <div class="control">
                    <input class="input" type="file" name="image" accept="image/*">
                </div>
                <?php if (!empty($teacher['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($teacher['image']) ?>" alt="Фото" class="mt-2" width="150">
                <?php endif; ?>
            </div>

            <div class="field">
                <div class="control">
                    <button class="button is-primary">Сохранить изменения</button>
                </div>
            </div>
        </form>
    </div>
</body>

</html>