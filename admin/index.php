<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Завершаем выполнение скрипта
}

include '../config.php';

// Получаем статистику с проверкой существования таблиц
$total_directors = 0;
$total_veterans = 0;
$total_projects = 0;
$total_media = 0;
$total_users = 0;

// Проверяем и получаем количество директоров
$directors_result = mysqli_query($conn, "SELECT * FROM directors");
if ($directors_result) {
    $total_directors = mysqli_num_rows($directors_result);
}

// Проверяем и получаем количество ветеранов
$veterans_result = mysqli_query($conn, "SELECT * FROM veterans");
if ($veterans_result) {
    $total_veterans = mysqli_num_rows($veterans_result);
}

// Проверяем и получаем количество проектов
$projects_result = mysqli_query($conn, "SELECT * FROM projects");
if ($projects_result) {
    $total_projects = mysqli_num_rows($projects_result);
}

// Проверяем и получаем количество публикаций СМИ
$media_result = mysqli_query($conn, "SELECT * FROM media_publications");
if ($media_result) {
    $total_media = mysqli_num_rows($media_result);
}

// Проверяем и получаем количество пользователей
$users_table_check = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
if (mysqli_num_rows($users_table_check) > 0) {
    $users_result = mysqli_query($conn, "SELECT * FROM users");
    if ($users_result) {
        $total_users = mysqli_num_rows($users_result);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            background: linear-gradient(120deg, #f5f7fa, #c3cfe2);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .admin-container {
            padding: 2rem 1rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .welcome-text {
            font-size: 1.1rem;
            color: #2d3436;
            margin-bottom: 0.5rem;
        }

        .main-title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 1rem;
            color: #636e72;
        }

        .admin-card {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
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

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            background: rgba(255, 255, 255, 0.2);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-description {
            color: #636e72;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
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

        .stats-section {
            background-color: #fff;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card {
            text-align: center;
            padding: 1.5rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            border: 1px solid #dee2e6;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0984e3;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #636e72;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .logout-section {
            text-align: center;
            margin-top: 3rem;
        }

        .logout-button {
            background-color: #e74c3c;
            border-color: #e74c3c;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logout-button:hover {
            background-color: #c0392b;
            border-color: #c0392b;
            color: white;
            transform: translateY(-2px);
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

        .admin-card {
            animation: slideIn 0.5s ease-out;
        }

        .admin-card:nth-child(1) { animation-delay: 0.1s; }
        .admin-card:nth-child(2) { animation-delay: 0.2s; }
        .admin-card:nth-child(3) { animation-delay: 0.3s; }
        .admin-card:nth-child(4) { animation-delay: 0.4s; }
        .admin-card:nth-child(5) { animation-delay: 0.5s; }
        .admin-card:nth-child(6) { animation-delay: 0.6s; }
        .admin-card:nth-child(7) { animation-delay: 0.7s; }
        .admin-card:nth-child(8) { animation-delay: 0.8s; }
        .admin-card:nth-child(9) { animation-delay: 0.9s; }

        /* Мобильная адаптивность */
        @media (max-width: 768px) {
            .admin-container {
                padding: 1rem 0.5rem;
            }

            .main-title {
                font-size: 2rem;
            }

            .card-body {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <!-- Заголовок -->
        <div class="header">
            <div class="welcome-text">Добро пожаловать, <?= $_SESSION['username']; ?>!</div>
            <h1 class="main-title">Админ панель</h1>
            <div class="subtitle">Управление контентом и данными</div>
        </div>

        <!-- Статистика -->
        <div class="stats-section">
            <div class="row g-3">
                <div class="col-md-2 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_directors ?></div>
                        <div class="stat-label">Директоров</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_veterans ?></div>
                        <div class="stat-label">Ветеранов</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_projects ?></div>
                        <div class="stat-label">Проектов</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_media ?></div>
                        <div class="stat-label">СМИ</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_users ?></div>
                        <div class="stat-label">Пользователей</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="stat-card">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Доступ</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Основные разделы -->
        <div class="row g-4">
            <!-- Директора -->
            <div class="col-lg-4 col-md-6">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <div class="card-icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            Директора
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-description">
                            Управление информацией о директорах организации, их биографиями и достижениями.
                        </p>
                        <a href="manage_directors.php" class="btn btn-primary w-100">
                            <i class="fas fa-cog me-2"></i>
                            Управление
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ветераны -->
            <div class="col-lg-4 col-md-6">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <div class="card-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                            Ветераны
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-description">
                            Управление информацией о ветеранах, их подвигах и вкладе в развитие организации.
                        </p>
                        <a href="manage_veterans.php" class="btn btn-primary w-100">
                            <i class="fas fa-cog me-2"></i>
                            Управление
                        </a>
                    </div>
                </div>
            </div>

            <!-- Проекты -->
            <div class="col-lg-4 col-md-6">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <div class="card-icon">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            Проекты
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-description">
                            Управление проектами организации, их описанием, статусом и результатами.
                        </p>
                        <a href="manage_projects.php" class="btn btn-primary w-100">
                            <i class="fas fa-cog me-2"></i>
                            Управление
                        </a>
                    </div>
                </div>
            </div>

            <!-- Специальности -->
            <div class="col-lg-4 col-md-6">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <div class="card-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            Специальности
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-description">
                            Управление образовательными специальностями, их описанием и требованиями.
                        </p>
                        <a href="manage_specialties.php" class="btn btn-primary w-100">
                            <i class="fas fa-cog me-2"></i>
                            Управление
                        </a>
                    </div>
                </div>
            </div>

            <!-- Преподаватели -->
            <div class="col-lg-4 col-md-6">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <div class="card-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            Преподаватели
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-description">
                            Управление информацией о преподавателях, их квалификации и достижениях.
                        </p>
                        <a href="manage_teachers.php" class="btn btn-primary w-100">
                            <i class="fas fa-cog me-2"></i>
                            Управление
                        </a>
                    </div>
                </div>
            </div>

            <!-- Студенты -->
            <div class="col-lg-4 col-md-6">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <div class="card-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            Студенты
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-description">
                            Управление информацией о студентах, их успехах и достижениях.
                        </p>
                        <a href="manage_students.php" class="btn btn-primary w-100">
                            <i class="fas fa-cog me-2"></i>
                            Управление
                        </a>
                    </div>
                </div>
            </div>

            <!-- Видео -->
            <div class="col-lg-4 col-md-6">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <div class="card-icon">
                                <i class="fas fa-video"></i>
                            </div>
                            Видео
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-description">
                            Управление видеоконтентом организации, загрузка и организация видеофайлов.
                        </p>
                        <a href="manage_video.php" class="btn btn-primary w-100">
                            <i class="fas fa-cog me-2"></i>
                            Управление
                        </a>
                    </div>
                </div>
            </div>

            <!-- Фото -->
            <div class="col-lg-4 col-md-6">
                <div class="admin-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <div class="card-icon">
                                <i class="fas fa-images"></i>
                            </div>
                            Фото
                        </h3>
                    </div>
                    <div class="card-body">
                        <p class="card-description">
                            Управление фотогалереей организации, загрузка и организация изображений.
                        </p>
                        <a href="manage_gallery.php" class="btn btn-primary w-100">
                            <i class="fas fa-cog me-2"></i>
                            Управление
                        </a>
                    </div>
                </div>
            </div>

            <!-- СМИ о нас -->
                    <div class="col-lg-4 col-md-6">
                        <div class="admin-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <div class="card-icon">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                    СМИ о нас
                                </h3>
                            </div>
                            <div class="card-body">
                                <p class="card-description">
                                    Управление публикациями в СМИ, добавление новых статей и упоминаний.
                                </p>
                                <a href="manage_media.php" class="btn btn-primary w-100">
                                    <i class="fas fa-cog me-2"></i>
                                    Управление
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="admin-card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <div class="card-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    Пользователи
                                </h3>
                            </div>
                            <div class="card-body">
                                <p class="card-description">
                                    Управление пользователями системы, ролями и правами доступа.
                                </p>
                                <a href="manage_users.php" class="btn btn-primary w-100">
                                    <i class="fas fa-cog me-2"></i>
                                    Управление
                                </a>
                            </div>
                        </div>
                    </div>
        </div>

        <!-- Дополнительные действия -->
        <div class="logout-section">
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="install_database.php" class="btn btn-outline-info">
                    <i class="fas fa-database me-2"></i>
                    Установка БД
                </a>
                <a href="update_database.php" class="btn btn-outline-warning">
                    <i class="fas fa-sync-alt me-2"></i>
                    Обновление БД
                </a>
                <a href="logout.php" class="logout-button">
                    <i class="fas fa-sign-out-alt"></i>
                    Выйти из системы
                </a>
            </div>
        </div>
    </div>
</body>

</html>