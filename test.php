<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Museum</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            min-height: 100vh;
            animation: gradientBackground 15s ease infinite;
            background: linear-gradient(-45deg, #a1c4fd, #c2e9fb, #fbc2eb, #ff9a9e);
            background-size: 400% 400%;
        }

        @keyframes gradientBackground {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 0 1rem;
        }

        .logo img {
            max-height: 200px;
        }

        .header {
            padding: 1rem 0;


            border-radius: 0 0 12px 12px;

        }

        .header__container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .language-switcher a {
            padding: 6px 14px;
            margin: 2px;
            border: 1px solid #ccc;
            border-radius: 30px;
            text-decoration: none;
            color: #333;
            background-color: #fff;
            font-weight: 500;
            transition: 0.3s;
        }

        .language-switcher a:hover,
        .language-switcher a.active {
            background-color: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            padding: 2rem 1rem;
        }

        .ContentBlock {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e2e8f0;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease-in-out;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .ContentBlock:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        }

        .ContentBlock i {
            font-size: 2.7rem;
            margin-bottom: 10px;
            color: #0d6efd;
        }

        .ContentBlock p.title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .btn-outline-primary {
            border-radius: 30px;
        }

        .developer {
            text-align: center;
            font-size: 0.9rem;
            margin: 2rem auto;
            color: #6c757d;
        }

        .developer a {
            color: inherit;
            text-decoration: none;
        }

        .developer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <header class="header">
            <div class="header__container">
                <a class="logo" href="/">
                    <img src="https://ff2.object.pscloud.io/giwycqkw/oeqsaqye/eoamgqcg/4384bf6eeeace361ca86ed2a4e97d2975c665532.png" alt="Logo">
                </a>
                <div class="language-switcher">
                    <a href="?lang=kk">Қазақша</a>
                    <a href="?lang=ru">Русский</a>
                    <a href="?lang=en">English</a>
                </div>
                <a class="btn btn-outline-primary" href="/">Назад</a>
            </div>
        </header>

        <main class="home__container">
            <div class="grid">
                <a class="ContentBlock" href="./pages/one.php">
                    <i class="bi bi-megaphone"></i>
                    <p class="title"><?= $lang['title']; ?></p>
                </a>
                <a class="ContentBlock" href="./pages/history.php">
                    <i class="bi bi-clock-history"></i>
                    <p class="title"><?= $lang['history']; ?></p>
                </a>
                <a class="ContentBlock" href="pages/historyName.php">
                    <i class="bi bi-people"></i>
                    <p class="title"><?= $lang['history_name']; ?></p>
                </a>
                <a class="ContentBlock" href="./pages/project.php">
                    <i class="bi bi-diagram-3"></i>
                    <p class="title"><?= $lang['projects']; ?></p>
                </a>
                <a class="ContentBlock" href="./pages/speciality.php">
                    <i class="bi bi-award"></i>
                    <p class="title"><?= $lang['specialties']; ?></p>
                </a>
                <a class="ContentBlock" href="./pages/teacher.php">
                    <i class="bi bi-person-badge-fill"></i>
                    <p class="title"><?= $lang['teachers']; ?></p>
                </a>
                <a class="ContentBlock" href="./pages/student.php">
                    <i class="bi bi-person-lines-fill"></i>
                    <p class="title"><?= $lang['students']; ?></p>
                </a>
                <a class="ContentBlock" href="./pages/gallery_video.php">
                    <i class="bi bi-camera-reels-fill"></i>
                    <p class="title"><?= $lang['videos']; ?></p>
                </a>
            </div>
        </main>
    </div>

    <footer class="developer">
        <a href="https://shotayev.kz/">Developer: @shotayev с 💗 КТСК</a>
    </footer>

</body>

</html>