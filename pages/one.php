<?php
$language = $_GET['lang'] ?? 'ru';
if ($language == 'kk') {
    include '../lang_kk.php';
} elseif ($language == 'en') {
    include '../lang_en.php';
} else {
    include '../lang_ru.php';
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $lang['title'] ?? 'Smart Page'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

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
            font-family: "Montserrat", sans-serif;
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
            max-height: 110px;
        }

        .language-switcher {
            display: flex;
            gap: 10px;
            margin-top: 0.5rem;
        }

        .language-switcher a {
            padding: 8px 16px;
            border-radius: 999px;
            border: 1px solid #ccc;
            text-decoration: none;
            background-color: var(--card-bg);
            color: var(--text-color);
            transition: 0.3s;
        }

        .language-switcher a:hover,
        .language-switcher a.active {
            background-color: var(--primary);
            color: #fff;
            border-color: var(--primary);
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

        .one__container {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .oneBlock {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            font-size: 1.1rem;
            line-height: 1.7;
        }

        .twoBlock {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .twoBlock .title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .twoBlockGrid {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            align-items: flex-start;
        }

        .twoBlockGrid img {
            max-width: 280px;
            border-radius: var(--radius);
            object-fit: cover;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .twoBlockGrid .text {
            flex: 1;
            font-size: 1rem;
            line-height: 1.8;
        }

        .theme-toggle {
            cursor: pointer;
            font-size: 1.4rem;
            margin-left: 1rem;
        }

        @media (max-width: 768px) {
            .twoBlockGrid {
                flex-direction: column;
                align-items: center;
            }

            .twoBlockGrid .text {
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <section class="header">
            <div class="header__container">
                <a class="logo" href="/">
                    <img src="../assets/img/logo.png" alt="KTSK Logo">
                </a>
                <div class="d-flex align-items-center flex-wrap gap-3">
                    <div class="language-switcher">
                        <a href="?lang=kk" class="<?= $language == 'kk' ? 'active' : '' ?>">Қазақша</a>
                        <a href="?lang=ru" class="<?= $language == 'ru' ? 'active' : '' ?>">Русский</a>
                        <a href="?lang=en" class="<?= $language == 'en' ? 'active' : '' ?>">English</a>
                    </div>
                    <div class="theme-toggle" onclick="toggleTheme()" title="Сменить тему">
                        <i class="bi bi-moon-stars-fill"></i>
                    </div>
                </div>
                <a class="back" href="/"><?= $lang['back']; ?></a>
            </div>
        </section>

        <section class="One">
            <div class="one__container">
                <div class="oneBlock">
                    <?= $lang['one_paragraph']; ?>
                </div>

                <div class="twoBlock">
                    <div class="title"><?= $lang['director_title']; ?></div>
                    <div class="twoBlockGrid">
                        <img src="../assets/img/director.jpg" alt="Director">
                        <div class="text">
                            <?= $lang['director_paragraph']; ?><br><br>
                            <?= $lang['teaching']; ?><br><br>
                            <?= $lang['developing']; ?><br><br>
                            <?= $lang['inspiring']; ?><br><br>
                            <?= $lang['college_vision']; ?><br><br>
                            <?= $lang['mission']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle("dark");
        }
    </script>
</body>

</html>