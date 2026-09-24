<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../config.php';

// Проверяем существование таблиц
$tables_exist = true;
$users_table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
$roles_table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'roles'");

if (mysqli_num_rows($users_table_exists) == 0 || mysqli_num_rows($roles_table_exists) == 0) {
    $tables_exist = false;
    $result = false;
} else {
    // Проверяем существование поля role_id в таблице users
    $check_role_id = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'role_id'");
    
    if (mysqli_num_rows($check_role_id) == 0) {
        // Если поле role_id не существует, получаем только пользователей
        $query = "SELECT u.*, 'user' as role_name, 'Пользователь' as role_description 
                  FROM users u 
                  ORDER BY u.created_at DESC";
    } else {
        // Если поле role_id существует, делаем JOIN с таблицей ролей
        $query = "SELECT u.*, r.name as role_name, r.description as role_description 
                  FROM users u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  ORDER BY u.created_at DESC";
    }
    
    $result = mysqli_query($conn, $query);
    
    // Проверяем на ошибки запроса
    if (!$result) {
        $error_message = "Ошибка базы данных: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
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

        .user-card {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .user-header {
            background: linear-gradient(135deg, #0984e3, #74b9ff);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .user-info h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }

        .user-email {
            opacity: 0.9;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .user-actions {
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

        .btn-success {
            background-color: #27ae60;
            border-color: #27ae60;
        }

        .btn-success:hover {
            background-color: #229954;
            border-color: #229954;
        }

        .user-body {
            padding: 1.5rem;
        }

        .user-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #636e72;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            color: #2d3436;
            font-weight: 500;
        }

        .role-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .role-admin {
            background-color: #e74c3c;
            color: white;
        }

        .role-editor {
            background-color: #f39c12;
            color: white;
        }

        .role-user {
            background-color: #27ae60;
            color: white;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
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

        .user-card {
            animation: slideIn 0.5s ease-out;
        }

        .user-card:nth-child(1) { animation-delay: 0.1s; }
        .user-card:nth-child(2) { animation-delay: 0.2s; }
        .user-card:nth-child(3) { animation-delay: 0.3s; }
        .user-card:nth-child(4) { animation-delay: 0.4s; }
        .user-card:nth-child(5) { animation-delay: 0.5s; }

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

            .user-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .user-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .user-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Заголовок страницы -->
        <div class="page-header">
            <h1 class="page-title">Управление пользователями</h1>
            <p class="page-subtitle">Добавление, редактирование и управление пользователями системы</p>
        </div>

        <!-- Кнопки действий -->
        <div class="action-buttons">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Назад
            </a>
            <a href="add_user.php" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>
                Добавить пользователя
            </a>
            <a href="manage_roles.php" class="btn btn-outline-secondary">
                <i class="fas fa-shield-alt me-2"></i>
                Управление ролями
            </a>
        </div>

        <!-- Список пользователей -->
        <?php if (!$tables_exist): ?>
            <div class="empty-state">
                <i class="fas fa-database"></i>
                <h3>Таблицы не созданы</h3>
                <p>Необходимо выполнить SQL скрипт для создания таблиц пользователей и ролей</p>
                <a href="create_users_roles_tables.sql" class="btn btn-primary mt-3" download>
                    <i class="fas fa-download me-2"></i>
                    Скачать SQL скрипт
                </a>
            </div>
        <?php elseif (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= $error_message ?>
            </div>
        <?php elseif ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="user-card">
                    <div class="user-header">
                        <div class="user-info">
                            <h3><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></h3>
                            <div class="user-email">@<?= htmlspecialchars($row['username']) ?> • <?= htmlspecialchars($row['email']) ?></div>
                        </div>
                        <div class="user-actions">
                            <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Редактировать
                            </a>
                            <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?')">
                                    <i class="fas fa-trash me-1"></i>
                                    Удалить
                                </a>
                            <?php endif; ?>
                            <?php if ($row['is_active']): ?>
                                <a href="toggle_user_status.php?id=<?= $row['id'] ?>&status=0" class="btn btn-danger btn-sm">
                                    <i class="fas fa-ban me-1"></i>
                                    Заблокировать
                                </a>
                            <?php else: ?>
                                <a href="toggle_user_status.php?id=<?= $row['id'] ?>&status=1" class="btn btn-success btn-sm">
                                    <i class="fas fa-check me-1"></i>
                                    Активировать
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="user-body">
                        <div class="user-details">
                            <div class="detail-item">
                                <div class="detail-label">Роль</div>
                                <div class="detail-value">
                                    <span class="role-badge role-<?= $row['role_name'] ?>">
                                        <?= ucfirst($row['role_name']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Статус</div>
                                <div class="detail-value">
                                    <span class="status-badge status-<?= $row['is_active'] ? 'active' : 'inactive' ?>">
                                        <?= $row['is_active'] ? 'Активен' : 'Заблокирован' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Последний вход</div>
                                <div class="detail-value">
                                    <?= $row['last_login'] ? date('d.m.Y H:i', strtotime($row['last_login'])) : 'Никогда' ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Дата создания</div>
                                <div class="detail-value">
                                    <?= date('d.m.Y', strtotime($row['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($row['role_description']): ?>
                            <div class="mt-2">
                                <small class="text-muted"><?= htmlspecialchars($row['role_description']) ?></small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>Пользователи не найдены</h3>
                <p>Добавьте первого пользователя в систему</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
