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
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/includes/topbar.php'; ?>

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
