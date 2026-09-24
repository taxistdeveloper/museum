<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Museum ktsk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f9f9f9;
            --card-bg: #ffffff;
            --text-color: #333;
            --primary: #0d6efd;
            --border-radius: 14px;
            --shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        body.dark {
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #e0e0e0;
            --primary: #91caff;
            --shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Montserrat', sans-serif;
            margin: 0;
            transition: background 0.3s, color 0.3s;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 1rem;
        }

        header.header {
            padding: 1.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo img {
            max-height: 140px;
        }

        .language-switcher a {
            margin: 0 6px;
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 500;
            text-decoration: none;
            color: var(--text-color);
            background: transparent;
            border: 1px solid #ccc;
            transition: 0.3s ease;
        }

        .language-switcher a:hover,
        .language-switcher a.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .theme-toggle {
            cursor: pointer;
            font-size: 1.4rem;
            margin-left: auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.8rem;
            margin-top: 2rem;
        }

        .ContentBlock {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 2rem 1.5rem;
            text-align: center;
            color: var(--text-color);
            text-decoration: none;
            box-shadow: var(--shadow);
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            border: 1px solid transparent;
        }

        .ContentBlock:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .ContentBlock i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .ContentBlock p.title {
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
        }

        .developer {
            text-align: center;
            font-size: 0.9rem;
            color: #888;
            margin: 2rem auto;
        }

        .developer a {
            text-decoration: none;
            color: inherit;
        }

        .developer a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <header class="header">
            <div class="d-flex align-items-center gap-4">
                <a class="logo" href="/">
                    <img src="https://ff2.object.pscloud.io/giwycqkw/oeqsaqye/eoamgqcg/4384bf6eeeace361ca86ed2a4e97d2975c665532.png" alt="Logo">
                </a>
                <div class="language-switcher">
                    <a href="?lang=kk">Қазақша</a>
                    <a href="?lang=ru" class="active">Русский</a>
                    <a href="?lang=en">English</a>
                </div>
            </div>
            <div class="theme-toggle" onclick="toggleTheme()" title="Сменить тему">
                <i class="bi bi-moon-stars-fill"></i>
            </div>
        </header>

        <main>
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

    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark');
        }
    </script>

</body>

</html>