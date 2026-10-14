<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $media_name = mysqli_real_escape_string($conn, $_POST['media_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $link = mysqli_real_escape_string($conn, $_POST['link']);
    $publication_date = $_POST['publication_date'];
    $media_type = $_POST['media_type'];

    if (empty($media_name) || empty($description) || empty($publication_date)) {
        $error = 'Пожалуйста, заполните все обязательные поля';
    } else {
        $query = "INSERT INTO media_publications (media_name, description, link, publication_date, media_type) 
                  VALUES ('$media_name', '$description', '$link', '$publication_date', '$media_type')";
        
        if (mysqli_query($conn, $query)) {
            $message = 'Публикация успешно добавлена!';
            // Очищаем форму
            $media_name = $description = $link = $publication_date = $media_type = '';
        } else {
            $error = 'Ошибка при добавлении публикации: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить публикацию СМИ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/includes/topbar.php'; ?>

    <div class="container">
        <!-- Заголовок страницы -->
        <header class="page-head">
            <div>
                <a class="back-link" href="manage_media.php">СМИ о нас</a>
                <h1>Новая публикация</h1>
                <p>Издание, дата и ссылка на материал.</p>
            </div>
        </header>

        <!-- Уведомления -->
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Форма -->
        <div class="form-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus"></i>
                    Новая публикация
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label" for="media_name">
                            Название СМИ <span class="required">*</span>
                        </label>
                        <input class="form-control" type="text" id="media_name" name="media_name" 
                               value="<?= isset($media_name) ? htmlspecialchars($media_name) : '' ?>" 
                               placeholder="Например: Казахстанская правда" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="media_type">
                            Тип СМИ
                        </label>
                        <select class="form-select" name="media_type" id="media_type">
                            <option value="newspaper" <?= (isset($media_type) && $media_type == 'newspaper') ? 'selected' : '' ?>>Газета</option>
                            <option value="tv" <?= (isset($media_type) && $media_type == 'tv') ? 'selected' : '' ?>>Телевидение</option>
                            <option value="radio" <?= (isset($media_type) && $media_type == 'radio') ? 'selected' : '' ?>>Радио</option>
                            <option value="online" <?= (isset($media_type) && $media_type == 'online') ? 'selected' : '' ?>>Онлайн-издание</option>
                            <option value="magazine" <?= (isset($media_type) && $media_type == 'magazine') ? 'selected' : '' ?>>Журнал</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="publication_date">
                            Дата публикации <span class="required">*</span>
                        </label>
                        <input class="form-control" type="date" id="publication_date" name="publication_date" 
                               value="<?= isset($publication_date) ? $publication_date : date('Y-m-d') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">
                            Описание публикации <span class="required">*</span>
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                                  placeholder="Краткое описание содержания публикации..." required><?= isset($description) ? htmlspecialchars($description) : '' ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="link">
                            Ссылка на публикацию
                        </label>
                        <input class="form-control" type="url" id="link" name="link" 
                               value="<?= isset($link) ? htmlspecialchars($link) : '' ?>" 
                               placeholder="https://example.com/article">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Сохранить
                        </button>
                        <a href="manage_media.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
