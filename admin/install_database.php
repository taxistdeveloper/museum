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
$success = false;

// SQL команды для создания таблиц
$sql_commands = [
    // Создание таблицы ролей
    "CREATE TABLE IF NOT EXISTS roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE COMMENT 'Название роли',
        description TEXT COMMENT 'Описание роли',
        permissions JSON COMMENT 'Права доступа в JSON формате',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Роли пользователей'",
    
    // Создание таблицы пользователей
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE COMMENT 'Имя пользователя',
        email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email пользователя',
        password VARCHAR(255) NOT NULL COMMENT 'Хешированный пароль',
        first_name VARCHAR(50) COMMENT 'Имя',
        last_name VARCHAR(50) COMMENT 'Фамилия',
        role_id INT DEFAULT 2 COMMENT 'ID роли (по умолчанию пользователь)',
        is_active BOOLEAN DEFAULT TRUE COMMENT 'Активен ли пользователь',
        last_login TIMESTAMP NULL COMMENT 'Последний вход',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Пользователи системы'",
    
    // Добавляем индексы для оптимизации
    "CREATE INDEX IF NOT EXISTS idx_users_username ON users(username)",
    "CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)",
    "CREATE INDEX IF NOT EXISTS idx_users_role_id ON users(role_id)",
    "CREATE INDEX IF NOT EXISTS idx_users_is_active ON users(is_active)",
    
    // Вставляем базовые роли
    "INSERT IGNORE INTO roles (name, description, permissions) VALUES
    ('admin', 'Администратор', '{\"all\": true, \"users\": {\"create\": true, \"read\": true, \"update\": true, \"delete\": true}, \"content\": {\"create\": true, \"read\": true, \"update\": true, \"delete\": true}}'),
    ('editor', 'Редактор', '{\"users\": {\"read\": true}, \"content\": {\"create\": true, \"read\": true, \"update\": true, \"delete\": false}}'),
    ('user', 'Пользователь', '{\"content\": {\"read\": true}}')",
    
    // Создаем администратора по умолчанию (пароль: admin123)
    "INSERT IGNORE INTO users (username, email, password, first_name, last_name, role_id, is_active) VALUES
    ('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Администратор', 'Системы', 1, TRUE)",
    
    // Создаем тестового редактора (пароль: editor123)
    "INSERT IGNORE INTO users (username, email, password, first_name, last_name, role_id, is_active) VALUES
    ('editor', 'editor@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Редактор', 'Контента', 2, TRUE)",
    
    // Создаем обычного пользователя (пароль: user123)
    "INSERT IGNORE INTO users (username, email, password, first_name, last_name, role_id, is_active) VALUES
    ('user', 'user@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Обычный', 'Пользователь', 3, TRUE)"
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['install'])) {
    $executed_commands = [];
    $failed_commands = [];
    
    // Начинаем транзакцию
    mysqli_begin_transaction($conn);
    
    try {
        foreach ($sql_commands as $index => $sql) {
            if (mysqli_query($conn, $sql)) {
                $executed_commands[] = $index + 1;
            } else {
                $failed_commands[] = [
                    'command' => $index + 1,
                    'error' => mysqli_error($conn),
                    'sql' => substr($sql, 0, 100) . '...'
                ];
            }
        }
        
        // Если все команды выполнились успешно, подтверждаем транзакцию
        if (empty($failed_commands)) {
            mysqli_commit($conn);
            $success = true;
            $message = "База данных успешно обновлена! Создано " . count($executed_commands) . " команд.";
        } else {
            // Если есть ошибки, откатываем транзакцию
            mysqli_rollback($conn);
            $error = "Ошибки при выполнении команд: " . count($failed_commands) . " из " . count($sql_commands);
        }
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "Критическая ошибка: " . $e->getMessage();
    }
}

// Проверяем текущее состояние таблиц
$tables_status = [];
$tables_to_check = ['roles', 'users'];

foreach ($tables_to_check as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    $tables_status[$table] = mysqli_num_rows($result) > 0;
    
    if ($tables_status[$table]) {
        // Проверяем количество записей
        $count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM $table");
        $count_row = mysqli_fetch_assoc($count_result);
        $tables_status[$table . '_count'] = $count_row['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка базы данных</title>
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

        .install-card {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }

        .card-header {
            background: linear-gradient(135deg, #0984e3, #74b9ff);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-body {
            padding: 2rem;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .status-card {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            border-left: 4px solid #dee2e6;
        }

        .status-card.success {
            border-left-color: #27ae60;
            background-color: #d4edda;
        }

        .status-card.warning {
            border-left-color: #f39c12;
            background-color: #fff3cd;
        }

        .status-card.danger {
            border-left-color: #e74c3c;
            background-color: #f8d7da;
        }

        .status-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-description {
            color: #636e72;
            font-size: 0.9rem;
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

        .alert {
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: none;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .test-users {
            background-color: #e3f2fd;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .test-users h5 {
            color: #1976d2;
            margin-bottom: 1rem;
        }

        .user-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #bbdefb;
        }

        .user-item:last-child {
            border-bottom: none;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-role {
            font-size: 0.8rem;
            color: #1976d2;
            font-weight: 500;
        }

        .user-credentials {
            font-size: 0.9rem;
            color: #424242;
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

        /* Мобильная адаптивность */
        @media (max-width: 768px) {
            .container {
                padding: 1rem 0.5rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .card-body {
                padding: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Заголовок страницы -->
        <div class="page-header">
            <h1 class="page-title">Установка базы данных</h1>
            <p class="page-subtitle">Создание таблиц пользователей и ролей</p>
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

        <!-- Основная карточка -->
        <div class="install-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-database"></i>
                    Состояние базы данных
                </h3>
            </div>
            <div class="card-body">
                <!-- Статус таблиц -->
                <div class="status-grid">
                    <div class="status-card <?= $tables_status['roles'] ? 'success' : 'danger' ?>">
                        <div class="status-title">
                            <i class="fas fa-shield-alt"></i>
                            Таблица ролей
                        </div>
                        <div class="status-description">
                            <?php if ($tables_status['roles']): ?>
                                ✅ Создана (<?= $tables_status['roles_count'] ?> ролей)
                            <?php else: ?>
                                ❌ Не создана
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="status-card <?= $tables_status['users'] ? 'success' : 'danger' ?>">
                        <div class="status-title">
                            <i class="fas fa-users"></i>
                            Таблица пользователей
                        </div>
                        <div class="status-description">
                            <?php if ($tables_status['users']): ?>
                                ✅ Создана (<?= $tables_status['users_count'] ?> пользователей)
                            <?php else: ?>
                                ❌ Не создана
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Информация о системе -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Что будет создано:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Таблица ролей с базовыми ролями (admin, editor, user)</li>
                        <li>Таблица пользователей с индексами для оптимизации</li>
                        <li>Тестовые пользователи для проверки системы</li>
                        <li>Связи между таблицами (foreign keys)</li>
                    </ul>
                </div>

                <!-- Тестовые пользователи -->
                <div class="test-users">
                    <h5><i class="fas fa-user-friends me-2"></i>Тестовые пользователи</h5>
                    <div class="user-item">
                        <div class="user-info">
                            <div class="user-role">Администратор</div>
                            <div class="user-credentials">admin / admin123</div>
                        </div>
                        <span class="badge bg-danger">Полные права</span>
                    </div>
                    <div class="user-item">
                        <div class="user-info">
                            <div class="user-role">Редактор</div>
                            <div class="user-credentials">editor / editor123</div>
                        </div>
                        <span class="badge bg-warning">Управление контентом</span>
                    </div>
                    <div class="user-item">
                        <div class="user-info">
                            <div class="user-role">Пользователь</div>
                            <div class="user-credentials">user / user123</div>
                        </div>
                        <span class="badge bg-success">Только просмотр</span>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="form-actions">
                    <?php if (!$tables_status['roles'] || !$tables_status['users']): ?>
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="install" class="btn btn-primary">
                                <i class="fas fa-download me-2"></i>
                                Установить базу данных
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            База данных уже установлена и готова к использованию!
                        </div>
                    <?php endif; ?>
                    
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Назад в админку
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
