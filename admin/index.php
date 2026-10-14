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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/includes/topbar.php'; ?>

    <div class="admin-home">
        <header class="page-head">
            <div>
                <div class="welcome-text">Здравствуйте, <?= htmlspecialchars($_SESSION['username']); ?></div>
                <h1>Разделы сайта</h1>
                <p>Выберите, что править. Цифры сверху ведут в тот же список.</p>
            </div>
        </header>

        <div class="stat-row">
            <a href="manage_directors.php"><strong><?= $total_directors ?></strong><span>директоров</span></a>
            <a href="manage_veterans.php"><strong><?= $total_veterans ?></strong><span>ветеранов</span></a>
            <a href="manage_projects.php"><strong><?= $total_projects ?></strong><span>проектов</span></a>
            <a href="manage_media.php"><strong><?= $total_media ?></strong><span>публикаций</span></a>
            <a href="manage_users.php"><strong><?= $total_users ?></strong><span>пользователей</span></a>
        </div>

        <section class="section-block">
            <h2>Люди</h2>
            <div class="link-grid">
                <a class="section-link" href="manage_directors.php">
                    <i class="bi bi-person-badge"></i>
                    <span><strong>Директора</strong><small>Имена, биографии и фото</small></span>
                </a>
                <a class="section-link" href="manage_veterans.php">
                    <i class="bi bi-award"></i>
                    <span><strong>Ветераны</strong><small>Карточки и биографии</small></span>
                </a>
                <a class="section-link" href="manage_teachers.php">
                    <i class="bi bi-easel"></i>
                    <span><strong>Преподаватели</strong><small>Должность, образование, фото</small></span>
                </a>
                <a class="section-link" href="manage_students.php">
                    <i class="bi bi-person-workspace"></i>
                    <span><strong>Студенты</strong><small>Имя, группа и фото</small></span>
                </a>
            </div>
        </section>

        <section class="section-block">
            <h2>Колледж</h2>
            <div class="link-grid">
                <a class="section-link" href="manage_specialties.php">
                    <i class="bi bi-mortarboard"></i>
                    <span><strong>Специальности</strong><small>Названия, описания и иконки</small></span>
                </a>
                <a class="section-link" href="manage_projects.php">
                    <i class="bi bi-diagram-3"></i>
                    <span><strong>Проекты</strong><small>Название, текст и дата</small></span>
                </a>
            </div>
        </section>

        <section class="section-block">
            <h2>Медиа</h2>
            <div class="link-grid">
                <a class="section-link" href="manage_video.php">
                    <i class="bi bi-camera-video"></i>
                    <span><strong>Видео</strong><small>Ролики и ссылки YouTube</small></span>
                </a>
                <a class="section-link" href="manage_gallery.php">
                    <i class="bi bi-images"></i>
                    <span><strong>Фото</strong><small>Галерея и подписи</small></span>
                </a>
                <a class="section-link" href="manage_media.php">
                    <i class="bi bi-newspaper"></i>
                    <span><strong>СМИ о нас</strong><small>Название, дата и ссылка</small></span>
                </a>
            </div>
        </section>

        <section class="section-block">
            <h2>Доступ</h2>
            <div class="link-grid">
                <a class="section-link" href="manage_users.php">
                    <i class="bi bi-people"></i>
                    <span><strong>Пользователи</strong><small>Кто входит в админку</small></span>
                </a>
                <a class="section-link" href="manage_roles.php">
                    <i class="bi bi-shield"></i>
                    <span><strong>Роли</strong><small>Права для пользователей</small></span>
                </a>
            </div>
        </section>

        <section class="section-block section-quiet">
            <h2>Служебное</h2>
            <div class="link-grid">
                <a class="section-link" href="install_database.php">
                    <i class="bi bi-database"></i>
                    <span><strong>Установка базы</strong><small>Создать таблицы пользователей</small></span>
                </a>
                <a class="section-link" href="update_database.php">
                    <i class="bi bi-arrow-repeat"></i>
                    <span><strong>Обновление базы</strong><small>Добавить недостающие поля</small></span>
                </a>
            </div>
        </section>
    </div>
</body>

</html>