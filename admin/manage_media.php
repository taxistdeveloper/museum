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
    <style>
        body {
            margin: 0;
            background: linear-gradient(120deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 2rem 1rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            font-size: 1rem;
            color: #636e72;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background-color: #0984e3;
            border-color: #0984e3;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0c74ca;
            border-color: #0c74ca;
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }

        .media-card {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .media-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .media-header {
            background: linear-gradient(135deg, #0984e3, #74b9ff);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .media-info h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .media-date {
            opacity: 0.9;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .media-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 6px;
        }

        .btn-warning {
            background-color: #f39c12;
            border-color: #f39c12;
        }

        .btn-warning:hover {
            background-color: #e67e22;
            border-color: #e67e22;
        }

        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }

        .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }

        .btn-info:hover {
            background-color: #138496;
            border-color: #138496;
        }

        .media-body {
            padding: 1.5rem;
        }

        .media-content {
            color: #636e72;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 4rem;
            color: #bdc3c7;
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            color: #636e72;
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #95a5a6;
        }

        /* Анимации */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .media-card {
            animation: slideIn 0.5s ease-out;
        }

        .media-card:nth-child(1) { animation-delay: 0.1s; }
        .media-card:nth-child(2) { animation-delay: 0.2s; }
        .media-card:nth-child(3) { animation-delay: 0.3s; }
        .media-card:nth-child(4) { animation-delay: 0.4s; }
        .media-card:nth-child(5) { animation-delay: 0.5s; }

        /* Мобильная адаптивность */
        @media (max-width: 768px) {
            .container {
                padding: 1rem 0.5rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .media-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .media-actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Заголовок страницы -->
        <div class="page-header">
            <h1 class="page-title">Управление СМИ о нас</h1>
            <p class="page-subtitle">Добавление и редактирование публикаций в средствах массовой информации</p>
        </div>

        <!-- Кнопки действий -->
        <div class="action-buttons">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Назад
            </a>
            <a href="add_media.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Добавить публикацию
            </a>
        </div>

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
