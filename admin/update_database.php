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

// SQL команды для обновления таблиц
$update_commands = [
    // Добавляем новые поля, если их нет
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS first_name VARCHAR(50) NULL COMMENT 'Имя'",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS last_name VARCHAR(50) NULL COMMENT 'Фамилия'",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(100) NULL COMMENT 'Email пользователя'",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE COMMENT 'Активен ли пользователь'",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL COMMENT 'Последний вход'",
    "ALTER TABLE users ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    "ALTER TABLE roles ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP",
    
    // Обновляем существующие поля, если нужно
    "ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL COMMENT 'Хешированный пароль'",
    "ALTER TABLE users MODIFY COLUMN is_active BOOLEAN DEFAULT TRUE COMMENT 'Активен ли пользователь'",
    
    // Добавляем индексы, если их нет
    "CREATE INDEX IF NOT EXISTS idx_users_username ON users(username)",
    "CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)",
    "CREATE INDEX IF NOT EXISTS idx_users_role_id ON users(role_id)",
    "CREATE INDEX IF NOT EXISTS idx_users_is_active ON users(is_active)",
    
    // Обновляем роли, если нужно
    "UPDATE roles SET permissions = '{\"all\": true, \"users\": {\"create\": true, \"read\": true, \"update\": true, \"delete\": true}, \"content\": {\"create\": true, \"read\": true, \"update\": true, \"delete\": true}}' WHERE name = 'admin'",
    "UPDATE roles SET permissions = '{\"users\": {\"read\": true}, \"content\": {\"create\": true, \"read\": true, \"update\": true, \"delete\": false}}' WHERE name = 'editor'",
    "UPDATE roles SET permissions = '{\"content\": {\"read\": true}}' WHERE name = 'user'"
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $executed_commands = [];
    $failed_commands = [];
    $skipped_commands = [];
    
    foreach ($update_commands as $index => $sql) {
        if (mysqli_query($conn, $sql)) {
            if (mysqli_affected_rows($conn) > 0) {
                $executed_commands[] = $index + 1;
            } else {
                $skipped_commands[] = $index + 1;
            }
        } else {
            $failed_commands[] = [
                'command' => $index + 1,
                'error' => mysqli_error($conn),
                'sql' => substr($sql, 0, 100) . '...'
            ];
        }
    }
    
    if (empty($failed_commands)) {
        $success = true;
        $message = "База данных успешно обновлена! Выполнено: " . count($executed_commands) . " команд, пропущено: " . count($skipped_commands) . " команд.";
    } else {
        $error = "Ошибки при выполнении команд: " . count($failed_commands) . " из " . count($update_commands);
    }
}

// Проверяем текущее состояние таблиц
$tables_info = [];
$tables_to_check = ['roles', 'users'];

foreach ($tables_to_check as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        // Получаем информацию о структуре таблицы
        $structure_result = mysqli_query($conn, "DESCRIBE $table");
        $columns = [];
        while ($row = mysqli_fetch_assoc($structure_result)) {
            $columns[] = $row['Field'];
        }
        
        // Получаем количество записей
        $count_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM $table");
        $count_row = mysqli_fetch_assoc($count_result);
        
        $tables_info[$table] = [
            'exists' => true,
            'columns' => $columns,
            'count' => $count_row['count']
        ];
    } else {
        $tables_info[$table] = ['exists' => false];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Обновление базы данных</title>
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

        .update-card {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
        }

        .card-header {
            background: linear-gradient(135deg, #f39c12, #e67e22);
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

        .table-info {
            background-color: #f8f9fa;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .table-title {
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .columns-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .column-item {
            background-color: #e9ecef;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-family: monospace;
        }

        .btn-primary {
            background-color: #f39c12;
            border-color: #f39c12;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #e67e22;
            border-color: #e67e22;
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

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
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

            .columns-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Заголовок страницы -->
        <div class="page-header">
            <h1 class="page-title">Обновление базы данных</h1>
            <p class="page-subtitle">Обновление структуры таблиц и добавление новых полей</p>
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
        <div class="update-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-sync-alt"></i>
                    Обновление структуры
                </h3>
            </div>
            <div class="card-body">
                <!-- Информация о таблицах -->
                <?php foreach ($tables_info as $table_name => $info): ?>
                    <div class="table-info">
                        <div class="table-title">
                            <i class="fas fa-table"></i>
                            Таблица: <?= $table_name ?>
                            <?php if ($info['exists']): ?>
                                <span class="badge bg-success">Существует</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Не найдена</span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($info['exists']): ?>
                            <p><strong>Записей:</strong> <?= $info['count'] ?></p>
                            <p><strong>Столбцы:</strong></p>
                            <div class="columns-list">
                                <?php foreach ($info['columns'] as $column): ?>
                                    <div class="column-item"><?= $column ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Таблица не найдена. Сначала выполните установку базы данных.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <!-- Предупреждение -->
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Внимание!</strong> Обновление может изменить структуру существующих таблиц. 
                    Рекомендуется создать резервную копию базы данных перед выполнением обновления.
                </div>

                <!-- Что будет обновлено -->
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Что будет обновлено:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Добавление новых полей (last_login, updated_at)</li>
                        <li>Обновление типов существующих полей</li>
                        <li>Добавление индексов для оптимизации</li>
                        <li>Обновление прав доступа в ролях</li>
                    </ul>
                </div>

                <!-- Кнопки действий -->
                <div class="form-actions">
                    <?php if ($tables_info['roles']['exists'] && $tables_info['users']['exists']): ?>
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="update" class="btn btn-primary">
                                <i class="fas fa-sync-alt me-2"></i>
                                Обновить базу данных
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Сначала необходимо установить базовые таблицы!
                        </div>
                    <?php endif; ?>
                    
                    <a href="install_database.php" class="btn btn-outline-secondary">
                        <i class="fas fa-database me-2"></i>
                        Установка БД
                    </a>
                    
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
