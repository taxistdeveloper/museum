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
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/includes/topbar.php'; ?>

    <div class="container">
        <!-- Заголовок страницы -->
        <header class="page-head">
            <div>
                <a class="back-link" href="manage_users.php">Пользователи</a>
                <h1>Роли</h1>
                <p>Набор прав, который назначается пользователю.</p>
            </div>
            <a href="add_role.php" class="btn btn-primary">Добавить</a>
        </header>

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
