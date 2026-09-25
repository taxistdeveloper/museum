<?php
require '../config.php';

// Получаем все проекты
$query_projects = "SELECT * FROM projects ORDER BY created_at DESC";
$result_projects = mysqli_query($conn, $query_projects);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Образовательные проекты</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f4f4f7;
            --card-bg: #fff;
            --text-color: #1f1f1f;
            --primary: #0d6efd;
            --shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            --radius: 12px;
        }

        body.dark {
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #e0e0e0;
            --primary: #91caff;
            --shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
        }

        body {
            margin: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s ease;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 1rem;
        }

        .header__container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            padding: 1rem 0;
        }

        .logo img {
            max-height: 140px;
        }

        .back {
            text-decoration: none;
            font-weight: 500;
            background-color: var(--primary);
            color: white;
            padding: 8px 20px;
            border-radius: var(--radius);
            transition: 0.3s;
        }

        .back:hover {
            background-color: #0056c7;
        }

        .Projects__container {
            margin-top: 2rem;
        }

        .projects__titlePage {
            font-size: 1.8rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 2rem;
        }

        .Projects__grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .ContentBlock {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-decoration: none;
            color: var(--text-color);
            transition: 0.3s;
        }

        .ContentBlock:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .ContentBlock .icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .ContentBlock .title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .theme-toggle {
            cursor: pointer;
            font-size: 1.2rem;
            padding: 8px 16px;
            border-radius: var(--radius);
            background-color: var(--primary);
            color: white;
            border: none;
            transition: 0.3s;
            margin-left: 1rem;
        }

        .theme-toggle:hover {
            background-color: #0056c7;
        }
    </style>

    <script>
        function toggleTheme() {
            document.body.classList.toggle("dark");
        }
    </script>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <section class="header">
            <div class="header__container">
                <a class="logo" href="#">
                    <img src="../assets/img/logo.png" alt="Логотип" />
                </a>
                <div class="right">
                    <a class="back" href="../index.php">Назад</a>
                    <button class="theme-toggle" onclick="toggleTheme()">🌓</button>
                </div>
            </div>
        </section>

        <!-- Projects Section -->
        <section class="Projects">
            <div class="Projects__container">
                <div class="projects__titlePage">
                    Образовательные проекты колледжа
                </div>

                <div class="Projects__grid">
                    <?php while ($project = mysqli_fetch_assoc($result_projects)): ?>
                        <a class="ContentBlock" href="../details/project_details.php?id=<?= $project['id'] ?>">
                            <div class="icon">📘</div>
                            <div class="title"><?= htmlspecialchars($project['name']) ?></div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    </div>
</body>

</html>