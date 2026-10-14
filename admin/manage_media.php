<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../config.php';

// Получаем все публикации СМИ
$query = "SELECT * FROM media_publications ORDER BY publication_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление СМИ</title>
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
                <a class="back-link" href="index.php">Разделы</a>
                <h1>СМИ о нас</h1>
                <p>Название издания, дата и ссылка на материал.</p>
            </div>
            <a href="add_media.php" class="btn btn-primary">Добавить</a>
        </header>

        <!-- Список публикаций -->
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="media-card">
                    <div class="media-header">
                        <div class="media-info">
                            <h3><?= htmlspecialchars($row['media_name']) ?></h3>
                            <div class="media-date"><?= date('d.m.Y', strtotime($row['publication_date'])) ?></div>
                        </div>
                        <div class="media-actions">
                            <a href="edit_media.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Редактировать
                            </a>
                            <a href="delete_media.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Вы уверены, что хотите удалить эту публикацию?')">
                                <i class="fas fa-trash me-1"></i>
                                Удалить
                            </a>
                        </div>
                    </div>
                    <div class="media-body">
                        <div class="media-content">
                            <?= htmlspecialchars($row['description']) ?>
                        </div>
                        <?php if (!empty($row['link'])): ?>
                            <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-external-link-alt me-1"></i>
                                Перейти к публикации
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h3>Публикации не найдены</h3>
                <p>Добавьте первую публикацию о вашей организации в СМИ</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
