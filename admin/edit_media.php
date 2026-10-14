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
$media_data = null;

// Получаем ID публикации
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: manage_media.php');
    exit();
}

// Получаем данные публикации
$query = "SELECT * FROM media_publications WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header('Location: manage_media.php');
    exit();
}

$media_data = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $media_name = mysqli_real_escape_string($conn, $_POST['media_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $link = mysqli_real_escape_string($conn, $_POST['link']);
    $publication_date = $_POST['publication_date'];
    $media_type = $_POST['media_type'];

    if (empty($media_name) || empty($description) || empty($publication_date)) {
        $error = 'Пожалуйста, заполните все обязательные поля';
    } else {
        $query = "UPDATE media_publications SET 
                  media_name = '$media_name',
                  description = '$description',
                  link = '$link',
                  publication_date = '$publication_date',
                  media_type = '$media_type'
                  WHERE id = $id";
        
        if (mysqli_query($conn, $query)) {
            $message = 'Публикация успешно обновлена!';
            // Обновляем данные
            $media_data['media_name'] = $media_name;
            $media_data['description'] = $description;
            $media_data['link'] = $link;
            $media_data['publication_date'] = $publication_date;
            $media_data['media_type'] = $media_type;
        } else {
            $error = 'Ошибка при обновлении публикации: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать публикацию СМИ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/includes/topbar.php'; ?>

    <div class="container">
        <!-- Заголовок страницы -->
        <div class="page-header">
            <a class="back-link" href="manage_media.php">СМИ о нас</a>
            <h1 class="page-title">Редактировать публикацию</h1>
            <p class="page-subtitle">Изменение информации о публикации в средствах массовой информации</p>
        </div>

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
                    <i class="fas fa-edit"></i>
                    Редактирование публикации
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label" for="media_name">
                            Название СМИ <span class="required">*</span>
                        </label>
                        <input class="form-control" type="text" id="media_name" name="media_name" 
                               value="<?= htmlspecialchars($media_data['media_name']) ?>" 
                               placeholder="Например: Казахстанская правда" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="media_type">
                            Тип СМИ
                        </label>
                        <select class="form-select" name="media_type" id="media_type">
                            <option value="newspaper" <?= ($media_data['media_type'] == 'newspaper') ? 'selected' : '' ?>>Газета</option>
                            <option value="tv" <?= ($media_data['media_type'] == 'tv') ? 'selected' : '' ?>>Телевидение</option>
                            <option value="radio" <?= ($media_data['media_type'] == 'radio') ? 'selected' : '' ?>>Радио</option>
                            <option value="online" <?= ($media_data['media_type'] == 'online') ? 'selected' : '' ?>>Онлайн-издание</option>
                            <option value="magazine" <?= ($media_data['media_type'] == 'magazine') ? 'selected' : '' ?>>Журнал</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="publication_date">
                            Дата публикации <span class="required">*</span>
                        </label>
                        <input class="form-control" type="date" id="publication_date" name="publication_date" 
                               value="<?= $media_data['publication_date'] ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="description">
                            Описание публикации <span class="required">*</span>
                        </label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                                  placeholder="Краткое описание содержания публикации..." required><?= htmlspecialchars($media_data['description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="link">
                            Ссылка на публикацию
                        </label>
                        <input class="form-control" type="url" id="link" name="link" 
                               value="<?= htmlspecialchars($media_data['link']) ?>" 
                               placeholder="https://example.com/article">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Сохранить изменения
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
