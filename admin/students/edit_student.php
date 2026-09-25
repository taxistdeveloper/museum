<?php
session_start();
require '../../config.php';

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Проверяем наличие ID студента в запросе
if (!isset($_GET['id'])) {
    echo "<h2 class='has-text-centered'>ID студента не указан</h2>";
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Получаем данные студента
$query_student = "SELECT * FROM students WHERE id = '$id'";
$result_student = mysqli_query($conn, $query_student);

if (mysqli_num_rows($result_student) === 0) {
    echo "<h2 class='has-text-centered'>Студент с указанным ID не найден</h2>";
    exit();
}

$student = mysqli_fetch_assoc($result_student);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);

    $group = mysqli_real_escape_string($conn, $_POST['group']);


    // Обработка достижений (массив)
    $achievements = $_POST['achievements'];
    $achievements_json = json_encode($achievements, JSON_UNESCAPED_UNICODE);

    // Обработка нового изображения (если загружено)
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = __DIR__ . '/uploads/' . $image;

        if (!move_uploaded_file($image_tmp, $image_path)) {
            echo "<h2 class='has-text-centered'>Ошибка загрузки файла</h2>";
            exit();
        }
    } else {
        $image = $student['image']; // Если новое изображение не загружено, оставить старое
    }

    // Обновляем данные студента
    $query_update = "UPDATE students SET 
                     name = '$name', 
                    
                     group_name = '$group', 
                    
                     achievements = '$achievements_json', 
                     image = '$image', 
                     updated_at = NOW() 
                     WHERE id = '$id'";

    if (mysqli_query($conn, $query_update)) {
        echo "<h2 class='has-text-centered'>Данные студента обновлены успешно!</h2>";
        echo "<a href='../manage_students.php' class='btn btn-primary'>Вернуться к списку студентов</a>";
        exit();
    } else {
        echo "<h2 class='has-text-centered'>Ошибка обновления данных</h2>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать студента</title>
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
        <h1 class="h2 text-center mt-5">Редактировать студента</h1>

        <form action="edit_student.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" class="card card-body">
            <div class="mb-3">
                <label class="form-label">Имя студента</label>
                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($student['name']) ?>" required>
                </div>


            <div class="mb-3">
                <label class="form-label">Группа</label>
                <input class="form-control" type="text" name="group" value="<?= htmlspecialchars($student['group_name']) ?>" required>
                </div>



            <div class="mb-3">
                <label class="form-label">Достижения</label>
                <div id="achievementFields">
                    <?php
                    $achievements = json_decode($student['achievements'], true) ?: [];
                    foreach ($achievements as $achievement) {
                        echo '<input class="form-control mt-2" type="text" name="achievements[]" value="' . htmlspecialchars($achievement) . '">';
                    }
                    ?>
                </div>
                <button type="button" class="btn btn-info mt-2" onclick="addAchievementField()">Добавить достижение</button>
            </div>

            <div class="mb-3">
                <label class="form-label">Фото студента</label>
                <input class="form-control" type="file" name="image" accept="image/*">
                    <p class="form-text">Если вы не загрузите новое изображение, останется текущее.</p>
                    <img src="uploads/<?= htmlspecialchars($student['image']) ?>" alt="Фото" style="width: 100px; height: 100px;">
                </div>

            <div class="mb-3">
                <button class="btn btn-primary">Сохранить изменения</button>
                </div>
        </form>

        <a href="../manage_students.php">
            <button class="btn btn-primary">Назад</button>
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>