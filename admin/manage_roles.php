<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../config.php';

// Получаем все роли
$result = false;
$roles_table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'roles'");
$users_table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'users'");

if (mysqli_num_rows($roles_table_exists) > 0) {
    if (mysqli_num_rows($users_table_exists) > 0) {
        // Проверяем существование поля role_id в таблице users
        $check_role_id = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'role_id'");
        
        if (mysqli_num_rows($check_role_id) > 0) {
            // Если поле role_id существует, делаем JOIN
            $query = "SELECT r.*, COUNT(u.id) as user_count 
                      FROM roles r 
                      LEFT JOIN users u ON r.id = u.role_id 
                      GROUP BY r.id 
                      ORDER BY r.id";
        } else {
            // Если поле role_id не существует, получаем только роли
            $query = "SELECT r.*, 0 as user_count 
                      FROM roles r 
                      ORDER BY r.id";
        }
    } else {
        // Если таблица users не существует, получаем только роли
        $query = "SELECT r.*, 0 as user_count 
                  FROM roles r 
                  ORDER BY r.id";
    }
    
    $result = mysqli_query($conn, $query);
    
    // Проверяем на ошибки
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
    <title>Управление ролями</title>
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
            max-width: 1000px;
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

        .role-card {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .role-header {
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .role-info h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            color: #2d3436;
        }

        .role-name {
            font-size: 0.9rem;
            color: #636e72;
            margin-top: 0.25rem;
        }

        .role-actions {
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

        .role-body {
            padding: 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .role-description {
            color: #636e72;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .role-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0984e3;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #636e72;
            font-weight: 500;
        }

        .permissions-list {
            margin-top: 1rem;
        }

        .permissions-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 0.5rem;
        }

        .permission-item {
            display: inline-block;
            background-color: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            margin: 0.25rem 0.25rem 0.25rem 0;
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

        .role-card {
            animation: slideIn 0.5s ease-out;
        }

        .role-card:nth-child(1) { animation-delay: 0.1s; }
        .role-card:nth-child(2) { animation-delay: 0.2s; }
        .role-card:nth-child(3) { animation-delay: 0.3s; }
        .role-card:nth-child(4) { animation-delay: 0.4s; }
        .role-card:nth-child(5) { animation-delay: 0.5s; }

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

            .role-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .role-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .role-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Заголовок страницы -->
        <div class="page-header">
            <h1 class="page-title">Управление ролями</h1>
            <p class="page-subtitle">Настройка ролей и прав доступа пользователей</p>
        </div>

        <!-- Кнопки действий -->
        <div class="action-buttons">
            <a href="manage_users.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Назад к пользователям
            </a>
            <a href="add_role.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Добавить роль
            </a>
        </div>

        <!-- Список ролей -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= $error_message ?>
            </div>
        <?php elseif ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="role-card">
                    <div class="role-header">
                        <div class="role-info">
                            <h3><?= ucfirst($row['name']) ?></h3>
                            <div class="role-name">ID: <?= $row['id'] ?></div>
                        </div>
                        <div class="role-actions">
                            <a href="edit_role.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Редактировать
                            </a>
                            <?php if ($row['user_count'] == 0): ?>
                                <a href="delete_role.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Вы уверены, что хотите удалить эту роль?')">
                                    <i class="fas fa-trash me-1"></i>
                                    Удалить
                                </a>
                            <?php else: ?>
                                <button class="btn btn-danger btn-sm" disabled title="Нельзя удалить роль с пользователями">
                                    <i class="fas fa-trash me-1"></i>
                                    Удалить
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="role-body">
                        <div class="role-description">
                            <?= htmlspecialchars($row['description']) ?>
                        </div>
                        
                        <div class="role-stats">
                            <div class="stat-item">
                                <div class="stat-number"><?= $row['user_count'] ?></div>
                                <div class="stat-label">Пользователей</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?= date('d.m.Y', strtotime($row['created_at'])) ?></div>
                                <div class="stat-label">Создана</div>
                            </div>
                        </div>

                        <?php if ($row['permissions']): ?>
                            <div class="permissions-list">
                                <div class="permissions-title">Права доступа:</div>
                                <?php 
                                $permissions = json_decode($row['permissions'], true);
                                if ($permissions && isset($permissions['all']) && $permissions['all']): ?>
                                    <span class="permission-item">Все права</span>
                                <?php else: ?>
                                    <?php foreach ($permissions as $category => $rights): ?>
                                        <?php if (is_array($rights)): ?>
                                            <?php foreach ($rights as $right => $value): ?>
                                                <?php if ($value): ?>
                                                    <span class="permission-item"><?= ucfirst($category) ?>: <?= ucfirst($right) ?></span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-shield-alt"></i>
                <h3>Роли не найдены</h3>
                <p>Добавьте первую роль в систему</p>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
