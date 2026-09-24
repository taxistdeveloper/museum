<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Админка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #2d3436;
            color: #fff;
            padding: 1.5rem;
            position: fixed;
            width: 250px;
        }

        .sidebar h3 {
            color: #fff;
            font-size: 1.6rem;
            margin-bottom: 2rem;
        }

        .sidebar a {
            display: block;
            color: #b2bec3;
            text-decoration: none;
            padding: 10px 0;
            transition: 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            color: #fff;
            font-weight: 600;
        }

        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
        }

        .topbar {
            background: #fff;
            padding: 1rem 2rem;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar .dropdown-menu {
            right: 0;
            left: auto;
        }

        .dashboard {
            padding: 2rem;
        }

        .card-box {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease;
            height: 100%;
        }

        .card-box:hover {
            transform: translateY(-5px);
        }

        .card-icon {
            font-size: 2rem;
            color: #0984e3;
            margin-bottom: 10px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .card-box a {
            text-decoration: none;
            color: #0984e3;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>⚙️ Admin</h3>
        <a href="manage_directors.php"><i class="fas fa-user-tie me-2"></i> Директора</a>
        <a href="manage_veterans.php"><i class="fas fa-medal me-2"></i> Ветераны</a>
        <a href="manage_projects.php"><i class="fas fa-project-diagram me-2"></i> Проекты</a>
        <a href="manage_specialties.php"><i class="fas fa-graduation-cap me-2"></i> Специальности</a>
        <a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher me-2"></i> Преподаватели</a>
        <a href="manage_students.php"><i class="fas fa-user-graduate me-2"></i> Студенты</a>
        <a href="manage_video.php"><i class="fas fa-video me-2"></i> Видео</a>
        <a href="manage_gallery.php"><i class="fas fa-image me-2"></i> Фото</a>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="topbar">
            <div class="fw-bold">Добро пожаловать, <?= $_SESSION['username']; ?></div>

            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i> Профиль
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Настройки</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Профиль</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="/admin/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Выход</a></li>
                </ul>
            </div>
        </div>

        <div class="dashboard container-fluid">
            <div class="row g-4">
                <?php
                $cards = [
                    ['title' => 'Директора', 'link' => 'manage_directors.php', 'icon' => 'fa-user-tie'],
                    ['title' => 'Ветераны', 'link' => 'manage_veterans.php', 'icon' => 'fa-medal'],
                    ['title' => 'Проекты', 'link' => 'manage_projects.php', 'icon' => 'fa-project-diagram'],
                    ['title' => 'Специальности', 'link' => 'manage_specialties.php', 'icon' => 'fa-graduation-cap'],
                    ['title' => 'Преподаватели', 'link' => 'manage_teachers.php', 'icon' => 'fa-chalkboard-teacher'],
                    ['title' => 'Студенты', 'link' => 'manage_students.php', 'icon' => 'fa-user-graduate'],
                    ['title' => 'Видео', 'link' => 'manage_video.php', 'icon' => 'fa-video'],
                    ['title' => 'Фото', 'link' => 'manage_gallery.php', 'icon' => 'fa-image'],
                ];
                foreach ($cards as $card): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card-box">
                            <div class="card-icon"><i class="fas <?= $card['icon'] ?>"></i></div>
                            <div class="card-title"><?= $card['title'] ?></div>
                            <a href="<?= $card['link'] ?>">Управлять →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>