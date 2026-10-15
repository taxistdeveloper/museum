<?php
session_start();
require '../../config.php';

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $group = trim((string)($_POST['group'] ?? ''));
    $achievements = $_POST['achievements'] ?? [];
    if (!is_array($achievements)) {
        $achievements = [];
    }
    $achievements = array_values(array_filter(array_map(static function ($item) {
        return trim((string)$item);
    }, $achievements), static function ($item) {
        return $item !== '';
    }));
    $achievements_json = json_encode($achievements, JSON_UNESCAPED_UNICODE);

    $upload_dir = __DIR__ . '/uploads/';
    $image_name = '';

    if ($name === '' || $group === '') {
        $error = 'Укажите имя и группу.';
    } elseif (!is_dir($upload_dir) && !mkdir($upload_dir, 0775, true)) {
        $error = 'Не удалось создать папку для фото.';
    } else {
        $file = $_FILES['image'] ?? [];
        $upload_error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        $tmp = $file['tmp_name'] ?? '';

        if ($upload_error !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
            $error = 'Не удалось загрузить файл. Код ошибки: ' . (int)$upload_error . '.';
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
            $ext_map = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];

            if (!isset($ext_map[$mime])) {
                $error = 'Недопустимый тип файла. Разрешены JPG, PNG, GIF и WEBP.';
            } else {
                $image_name = bin2hex(random_bytes(16)) . '.' . $ext_map[$mime];
                if (!move_uploaded_file($tmp, $upload_dir . $image_name)) {
                    $reason = error_get_last()['message'] ?? 'нет прав на запись в папку uploads';
                    $error = 'Не удалось сохранить файл. ' . $reason;
                    $image_name = '';
                }
            }
        }
    }

    if ($error === '' && $image_name !== '') {
        try {
            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO students (name, group_name, achievements, image, created_at) VALUES (?, ?, ?, ?, NOW())"
            );
            mysqli_stmt_bind_param($stmt, 'ssss', $name, $group, $achievements_json, $image_name);
            mysqli_stmt_execute($stmt);
            $_SESSION['message'] = 'Студент добавлен успешно!';
            header('Location: ../manage_students.php');
            exit;
        } catch (mysqli_sql_exception $e) {
            @unlink($upload_dir . $image_name);
            $error = 'Ошибка добавления студента: ' . $e->getMessage();
        }
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
    <link rel="stylesheet" href="../../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/../includes/topbar.php'; ?>

    <div class="container">
        <header class="page-head">
            <div>
                <a class="back-link" href="../manage_students.php">Студенты</a>
                <h1>Новый студент</h1>
                <p>Имя, группа и фото.</p>
            </div>
        </header>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

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