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

$existing_columns = [];
$columns_result = mysqli_query($conn, "SHOW COLUMNS FROM users");
if ($columns_result) {
    while ($row = mysqli_fetch_assoc($columns_result)) {
        $existing_columns[] = $row['Field'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name'] ?? '');
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name'] ?? '');
    $role_id = (int)($_POST['role_id'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $needs_name = in_array('first_name', $existing_columns, true) || in_array('last_name', $existing_columns, true);

    // Валидация
    if (empty($username) || empty($password) || ($needs_name && (empty($first_name) || empty($last_name)))) {
        $error = 'Пожалуйста, заполните все обязательные поля';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен содержать минимум 6 символов';
    } else {
        // Проверяем email если поле существует
        if (in_array('email', $existing_columns) && isset($_POST['email'])) {
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Некорректный email адрес';
            }
        }

        if (!isset($error)) {
            // Проверяем, не существует ли уже такой пользователь
            $check_query = "SELECT id FROM users WHERE username = '$username'";
            if (in_array('email', $existing_columns) && isset($_POST['email'])) {
                $check_query .= " OR email = '$email'";
            }
            $check_result = mysqli_query($conn, $check_query);
            
            if ($check_result && mysqli_num_rows($check_result) > 0) {
                $error = 'Пользователь с таким именем уже существует';
            } else {
                // Хешируем пароль
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $fields = "username, password";
                $values = "'$username', '$hashed_password'";
                if (in_array('first_name', $existing_columns, true)) {
                    $fields .= ", first_name";
                    $values .= ", '$first_name'";
                }
                if (in_array('last_name', $existing_columns, true)) {
                    $fields .= ", last_name";
                    $values .= ", '$last_name'";
                }
                
                if (in_array('email', $existing_columns) && isset($_POST['email'])) {
                    $fields .= ", email";
                    $values .= ", '$email'";
                }
                
                if (in_array('role_id', $existing_columns)) {
                    $fields .= ", role_id";
                    $values .= ", $role_id";
                }
                
                if (in_array('is_active', $existing_columns)) {
                    $fields .= ", is_active";
                    $values .= ", $is_active";
                }
                
                $query = "INSERT INTO users ($fields) VALUES ($values)";
                
                if (mysqli_query($conn, $query)) {
                    $message = 'Пользователь успешно добавлен!';
                    // Очищаем форму
                    $username = $password = $confirm_password = $first_name = $last_name = '';
                    $role_id = 2; // По умолчанию редактор
                    $is_active = 1;
                } else {
                    $error = 'Ошибка при добавлении пользователя: ' . mysqli_error($conn);
                }
            }
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
    <title>Добавить пользователя</title>
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
            background: linear-gradient(135deg, #27ae60, #2ecc71);
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

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        .strength-weak { color: #e74c3c; }
        .strength-medium { color: #f39c12; }
        .strength-strong { color: #27ae60; }

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
            <h1 class="page-title">Добавить пользователя</h1>
            <p class="page-subtitle">Создание нового пользователя в системе</p>
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
                    <i class="fas fa-user-plus"></i>
                    Новый пользователь
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <?php if (in_array('first_name', $existing_columns, true) || in_array('last_name', $existing_columns, true)): ?>
                    <div class="row">
                        <?php if (in_array('first_name', $existing_columns, true)): ?>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="first_name">
                                    Имя <span class="required">*</span>
                                </label>
                                <input class="form-control" type="text" id="first_name" name="first_name" 
                                       value="<?= isset($first_name) ? htmlspecialchars($first_name) : '' ?>" 
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
                                       value="<?= isset($last_name) ? htmlspecialchars($last_name) : '' ?>" 
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
                                       value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" 
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
                                       value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" 
                                       placeholder="Введите email" required>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="password">
                                    Пароль <span class="required">*</span>
                                </label>
                                <input class="form-control" type="password" id="password" name="password" 
                                       placeholder="Минимум 6 символов" required>
                                <div class="password-strength" id="password-strength"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="confirm_password">
                                    Подтвердите пароль <span class="required">*</span>
                                </label>
                                <input class="form-control" type="password" id="confirm_password" name="confirm_password" 
                                       placeholder="Повторите пароль" required>
                            </div>
                        </div>
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
                                            <option value="<?= $role['id'] ?>" <?= (isset($role_id) && $role_id == $role['id']) ? 'selected' : '' ?>>
                                                <?= ucfirst($role['name']) ?> - <?= htmlspecialchars($role['description']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                <?php else: ?>
                                    <input class="form-control" type="hidden" name="role_id" value="1">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Таблица ролей не найдена. Пользователь будет создан с ролью администратора.
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
                                           <?= (isset($is_active) && $is_active) ? 'checked' : 'checked' ?>>
                                    <label class="form-check-label" for="is_active">
                                        Активный пользователь
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Создать пользователя
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
        // Проверка силы пароля
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('password-strength');
            
            if (password.length === 0) {
                strengthDiv.textContent = '';
                return;
            }
            
            let strength = 0;
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            if (strength < 2) {
                strengthDiv.textContent = 'Слабый пароль';
                strengthDiv.className = 'password-strength strength-weak';
            } else if (strength < 4) {
                strengthDiv.textContent = 'Средний пароль';
                strengthDiv.className = 'password-strength strength-medium';
            } else {
                strengthDiv.textContent = 'Сильный пароль';
                strengthDiv.className = 'password-strength strength-strong';
            }
        });

        // Проверка совпадения паролей
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Пароли не совпадают');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>

</html>
