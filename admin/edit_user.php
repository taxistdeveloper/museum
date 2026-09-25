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

// Получаем ID пользователя из URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    header('Location: manage_users.php');
    exit();
}

// Получаем данные пользователя
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_query);

if (!$user_result || mysqli_num_rows($user_result) == 0) {
    header('Location: manage_users.php');
    exit();
}

$user_data = mysqli_fetch_assoc($user_result);

$existing_columns = [];
$columns_result = mysqli_query($conn, "SHOW COLUMNS FROM users");
if ($columns_result) {
    while ($row = mysqli_fetch_assoc($columns_result)) {
        $existing_columns[] = $row['Field'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $first_name = trim((string)($_POST['first_name'] ?? ''));
    $last_name = trim((string)($_POST['last_name'] ?? ''));
    $role_id = (int)($_POST['role_id'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $password = (string)($_POST['password'] ?? '');
    $confirm_password = (string)($_POST['confirm_password'] ?? '');
    $change_password = ($password !== '' || $confirm_password !== '');

    $needs_name = in_array('first_name', $existing_columns, true) || in_array('last_name', $existing_columns, true);

    if ($username === '' || ($needs_name && ($first_name === '' || $last_name === ''))) {
        $error = 'Пожалуйста, заполните все обязательные поля';
    } elseif ($change_password && $password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif ($change_password && strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов';
    } else {
        $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($check_stmt, "si", $username, $user_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $error = 'Пользователь с таким именем уже существует';
        } else {
            $fields = ['username = ?'];
            $types = 's';
            $values = [$username];

            if (in_array('first_name', $existing_columns, true)) {
                $fields[] = 'first_name = ?';
                $types .= 's';
                $values[] = $first_name;
            }
            if (in_array('last_name', $existing_columns, true)) {
                $fields[] = 'last_name = ?';
                $types .= 's';
                $values[] = $last_name;
            }

            if (in_array('email', $existing_columns, true) && isset($_POST['email'])) {
                $email = trim((string)$_POST['email']);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Некорректный email адрес';
                } else {
                    $fields[] = 'email = ?';
                    $types .= 's';
                    $values[] = $email;
                }
            }

            if (in_array('role_id', $existing_columns, true) && $role_id > 0) {
                $fields[] = 'role_id = ?';
                $types .= 'i';
                $values[] = $role_id;
            }

            if (in_array('is_active', $existing_columns, true)) {
                $fields[] = 'is_active = ?';
                $types .= 'i';
                $values[] = $is_active;
            }

            if ($change_password && $error === '') {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $fields[] = '`password` = ?';
                $types .= 's';
                $values[] = $hashed_password;
            }

            if ($error === '') {
                $types .= 'i';
                $values[] = $user_id;
                $query = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
                $stmt = mysqli_prepare($conn, $query);
                if ($stmt && mysqli_stmt_bind_param($stmt, $types, ...$values) && mysqli_stmt_execute($stmt)) {
                    $message = $change_password
                        ? 'Пользователь обновлён, пароль изменён.'
                        : 'Пользователь успешно обновлен!';
                    $user_data['username'] = $username;
                    $user_data['first_name'] = $first_name;
                    $user_data['last_name'] = $last_name;
                    if (isset($email)) {
                        $user_data['email'] = $email;
                    }
                    if (in_array('role_id', $existing_columns, true) && $role_id > 0) {
                        $user_data['role_id'] = $role_id;
                    }
                    if (in_array('is_active', $existing_columns, true)) {
                        $user_data['is_active'] = $is_active;
                    }
                    $password = '';
                    $confirm_password = '';
                    $change_password = false;
                } else {
                    $error = 'Ошибка при обновлении пользователя: ' . mysqli_error($conn);
                }
            }
        }
        if (isset($check_stmt)) {
            mysqli_stmt_close($check_stmt);
        }
    }
}

// Получаем список ролей
$roles_result = false;
$roles_table_exists = mysqli_query($conn, "SHOW TABLES LIKE 'roles'");

if (mysqli_num_rows($roles_table_exists) > 0) {
    $roles_query = "SELECT * FROM roles ORDER BY id";
    $roles_result = mysqli_query($conn, $roles_query);
    
    // Проверяем на ошибки
    if (!$roles_result) {
        $error = "Ошибка при получении списка ролей: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать пользователя</title>
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
            max-width: 800px;
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

        .form-card {
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

        .form-label {
            font-weight: 500;
            color: #2d3436;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #0984e3;
            box-shadow: 0 0 0 0.2rem rgba(9, 132, 227, 0.25);
        }

        .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: #0984e3;
            box-shadow: 0 0 0 0.2rem rgba(9, 132, 227, 0.25);
        }

        .form-check {
            margin-bottom: 1rem;
        }

        .form-check-input {
            width: 1.2rem;
            height: 1.2rem;
            margin-top: 0.1rem;
        }

        .form-check-label {
            font-weight: 500;
            color: #2d3436;
            margin-left: 0.5rem;
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

        .required {
            color: #e74c3c;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .password-section {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .password-toggle {
            cursor: pointer;
            color: #0984e3;
            text-decoration: none;
        }

        .password-toggle:hover {
            text-decoration: underline;
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
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Заголовок страницы -->
        <div class="page-header">
            <h1 class="page-title">Редактировать пользователя</h1>
            <p class="page-subtitle">Изменение информации о пользователе</p>
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
                    <i class="fas fa-user-edit"></i>
                    Редактирование пользователя
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="edit_user.php?id=<?= (int)$user_id ?>">
                    <?php if (in_array('first_name', $existing_columns, true) || in_array('last_name', $existing_columns, true)): ?>
                    <div class="row">
                        <?php if (in_array('first_name', $existing_columns, true)): ?>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="first_name">
                                    Имя <span class="required">*</span>
                                </label>
                                <input class="form-control" type="text" id="first_name" name="first_name" 
                                       value="<?= htmlspecialchars($user_data['first_name'] ?? '') ?>" 
                                       placeholder="Введите имя" required>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if (in_array('last_name', $existing_columns, true)): ?>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="last_name">
                                    Фамилия <span class="required">*</span>
                                </label>
                                <input class="form-control" type="text" id="last_name" name="last_name" 
                                       value="<?= htmlspecialchars($user_data['last_name'] ?? '') ?>" 
                                       placeholder="Введите фамилию" required>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="username">
                                    Имя пользователя <span class="required">*</span>
                                </label>
                                <input class="form-control" type="text" id="username" name="username" 
                                       value="<?= htmlspecialchars($user_data['username']) ?>" 
                                       placeholder="Введите имя пользователя" required>
                            </div>
                        </div>
                        <?php if (in_array('email', $existing_columns)): ?>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="email">
                                    Email <span class="required">*</span>
                                </label>
                                <input class="form-control" type="email" id="email" name="email" 
                                       value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" 
                                       placeholder="Введите email" required>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (in_array('role_id', $existing_columns) || in_array('is_active', $existing_columns)): ?>
                    <div class="row">
                        <?php if (in_array('role_id', $existing_columns)): ?>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="role_id">
                                    Роль
                                </label>
                                <?php if ($roles_result && mysqli_num_rows($roles_result) > 0): ?>
                                    <select class="form-select" name="role_id" id="role_id">
                                        <?php while ($role = mysqli_fetch_assoc($roles_result)): ?>
                                            <option value="<?= $role['id'] ?>" <?= ($user_data['role_id'] == $role['id']) ? 'selected' : '' ?>>
                                                <?= ucfirst($role['name']) ?> - <?= htmlspecialchars($role['description']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                <?php else: ?>
                                    <input class="form-control" type="hidden" name="role_id" value="<?= $user_data['role_id'] ?? 1 ?>">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Таблица ролей не найдена. Роль пользователя не будет изменена.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if (in_array('is_active', $existing_columns)): ?>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Статус</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                           <?= $user_data['is_active'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">
                                        Активный пользователь
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="password-section">
                        <div class="mb-3">
                            <strong>Изменить пароль</strong>
                            <div class="text-muted small">Оставьте пустым, если пароль менять не нужно</div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="password">Новый пароль</label>
                                    <input class="form-control" type="password" id="password" name="password"
                                           placeholder="Минимум 6 символов" autocomplete="new-password"
                                           minlength="6">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label" for="confirm_password">Подтвердите пароль</label>
                                    <input class="form-control" type="password" id="confirm_password" name="confirm_password"
                                           placeholder="Повторите пароль" autocomplete="new-password"
                                           minlength="6">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Сохранить изменения
                        </button>
                        <a href="manage_users.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Отмена
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        function validatePasswords() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (password && password.length < 6) {
                passwordInput.setCustomValidity('Пароль должен содержать минимум 6 символов');
            } else {
                passwordInput.setCustomValidity('');
            }

            if (confirmPassword && password !== confirmPassword) {
                confirmPasswordInput.setCustomValidity('Пароли не совпадают');
            } else if (password && !confirmPassword) {
                confirmPasswordInput.setCustomValidity('Повторите пароль');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        }

        passwordInput.addEventListener('input', validatePasswords);
        confirmPasswordInput.addEventListener('input', validatePasswords);
    </script>
</body>

</html>
