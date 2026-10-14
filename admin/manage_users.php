<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../config.php';

function museum_user_columns(mysqli $conn): array
{
    $columns = [];
    $columns_result = mysqli_query($conn, "SHOW COLUMNS FROM users");
    if ($columns_result) {
        while ($column = mysqli_fetch_assoc($columns_result)) {
            $columns[] = $column['Field'];
        }
    }
    return $columns;
}

function museum_has_column(array $columns, string $name): bool
{
    return in_array($name, $columns, true);
}

// Проверяем существование таблиц
$tables_exist = true;
$existing_columns = [];
$users_table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
$roles_table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'roles'");

if (mysqli_num_rows($users_table_exists) == 0 || mysqli_num_rows($roles_table_exists) == 0) {
    $tables_exist = false;
    $result = false;
} else {
    $existing_columns = museum_user_columns($conn);
    $order_by = museum_has_column($existing_columns, 'created_at') ? 'u.created_at DESC' : 'u.id DESC';

    if (!museum_has_column($existing_columns, 'role_id')) {
        $query = "SELECT u.*, 'user' as role_name, 'Пользователь' as role_description 
                  FROM users u 
                  ORDER BY $order_by";
    } else {
        $query = "SELECT u.*, r.name as role_name, r.description as role_description 
                  FROM users u 
                  LEFT JOIN roles r ON u.role_id = r.id 
                  ORDER BY $order_by";
    }

    $result = mysqli_query($conn, $query);

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
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/includes/topbar.php'; ?>

    <div class="container">
        <!-- Заголовок страницы -->
        <header class="page-head">
            <div>
                <a class="back-link" href="index.php">Разделы</a>
                <h1>Пользователи</h1>
                <p>Кто может входить в админку. Роли настраиваются отдельно.</p>
            </div>
            <div class="action-buttons">
                <a href="manage_roles.php" class="btn btn-light">Роли</a>
                <a href="add_user.php" class="btn btn-primary">Добавить</a>
            </div>
        </header>

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
                <?php
                    $username = (string)($row['username'] ?? '');
                    $first_name = museum_has_column($existing_columns, 'first_name') ? trim((string)($row['first_name'] ?? '')) : '';
                    $last_name = museum_has_column($existing_columns, 'last_name') ? trim((string)($row['last_name'] ?? '')) : '';
                    $display_name = trim($first_name . ' ' . $last_name);
                    if ($display_name === '') {
                        $display_name = $username !== '' ? $username : 'Пользователь';
                    }
                    $email = museum_has_column($existing_columns, 'email') ? trim((string)($row['email'] ?? '')) : '';
                    $is_active = museum_has_column($existing_columns, 'is_active') ? !empty($row['is_active']) : true;
                    $last_login = museum_has_column($existing_columns, 'last_login') ? ($row['last_login'] ?? null) : null;
                    $created_at = $row['created_at'] ?? null;
                    $role_name = strtolower((string)($row['role_name'] ?? 'user'));
                    $role_description = (string)($row['role_description'] ?? '');
                ?>
                <div class="user-card">
                    <div class="user-header">
                        <div class="user-info">
                            <h3><?= htmlspecialchars($display_name) ?></h3>
                            <div class="user-email">
                                @<?= htmlspecialchars($username) ?>
                                <?php if ($email !== ''): ?>
                                    • <?= htmlspecialchars($email) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="user-actions">
                            <a href="edit_user.php?id=<?= (int)$row['id'] ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>
                                Редактировать
                            </a>
                            <?php if ((int)$row['id'] !== (int)$_SESSION['user_id']): ?>
                                <a href="delete_user.php?id=<?= (int)$row['id'] ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Вы уверены, что хотите удалить этого пользователя?')">
                                    <i class="fas fa-trash me-1"></i>
                                    Удалить
                                </a>
                            <?php endif; ?>
                            <?php if (museum_has_column($existing_columns, 'is_active')): ?>
                                <?php if ($is_active): ?>
                                    <a href="toggle_user_status.php?id=<?= (int)$row['id'] ?>&status=0" class="btn btn-danger btn-sm">
                                        <i class="fas fa-ban me-1"></i>
                                        Заблокировать
                                    </a>
                                <?php else: ?>
                                    <a href="toggle_user_status.php?id=<?= (int)$row['id'] ?>&status=1" class="btn btn-success btn-sm">
                                        <i class="fas fa-check me-1"></i>
                                        Активировать
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="user-body">
                        <div class="user-details">
                            <div class="detail-item">
                                <div class="detail-label">Роль</div>
                                <div class="detail-value">
                                    <span class="role-badge role-<?= htmlspecialchars($role_name) ?>">
                                        <?= htmlspecialchars(ucfirst($role_name)) ?>
                                    </span>
                                </div>
                            </div>
                            <?php if (museum_has_column($existing_columns, 'is_active')): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Статус</div>
                                    <div class="detail-value">
                                        <span class="status-badge status-<?= $is_active ? 'active' : 'inactive' ?>">
                                            <?= $is_active ? 'Активен' : 'Заблокирован' ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (museum_has_column($existing_columns, 'last_login')): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Последний вход</div>
                                    <div class="detail-value">
                                        <?= $last_login ? date('d.m.Y H:i', strtotime((string)$last_login)) : 'Никогда' ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($created_at): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Дата создания</div>
                                    <div class="detail-value">
                                        <?= date('d.m.Y', strtotime((string)$created_at)) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($role_description !== ''): ?>
                            <div class="mt-2">
                                <small class="text-muted"><?= htmlspecialchars($role_description) ?></small>
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
