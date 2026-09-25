<?php
require '../config.php';

// Получаем выбранный язык (по умолчанию русский)
$lang = $_GET['lang'] ?? 'ru';

// Получаем всех ветеранов в алфавитном порядке по выбранному языку
$query_veterans = "
    SELECT * 
    FROM veterans 
    ORDER BY LOWER(COALESCE(name_$lang, name_ru)) ASC";
$result_veterans = mysqli_query($conn, $query_veterans);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $lang == 'kz' ? 'Колледж ардагерлері' : ($lang == 'en' ? 'College Veterans' : 'Ветераны колледжа') ?></title>

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
            max-height: 80px;
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

        .language-switcher {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            padding: 1rem 0;
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

        .theme-toggle {
            cursor: pointer;
            font-size: 1.4rem;
            margin-left: 1rem;
        }

        .historyPeople__title {
            font-size: 2rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 2rem;
        }

        .humansGrid {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
        }

        .HumanCard {
            background-color: var(--card-bg);
            box-shadow: var(--shadow);
            border-radius: var(--radius);
            overflow: hidden;
            width: 300px;
            transition: transform 0.3s ease;
        }

        .HumanCard:hover {
            transform: translateY(-5px);
        }

        .HumanCard .image img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .HumanCard .right {
            padding: 1rem;
        }

        .historyPeople__name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .historyPeople__button a {
            text-decoration: none;
            color: #fff;
            background-color: var(--primary);
            padding: 8px 16px;
            border-radius: var(--radius);
            display: inline-block;
            transition: 0.3s;
        }

        .historyPeople__button a:hover {
            background-color: #0056c7;
        }

        @media (max-width: 768px) {
            .HumanCard {
                width: 100%;
            }
        }

        .humansGrid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        .veteranCard {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .veteranCard:hover {
            transform: translateY(-5px);
        }

        .veteranCard img {
            width: 100%;
            height: 260px;
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }

        .veteranCard h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-color);
            margin: 1rem 0 0.5rem;
            padding: 0 1rem;
        }

        .detailsButton {
            display: inline-block;
            margin: 1rem auto 1.5rem;
            padding: 8px 16px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .detailsButton:hover {
            background: #0056c7;
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
        <section class="header">
            <div class="header__container">
                <a class="logo" href="#">
                    <img src="../assets/img/logo.png" alt="Логотип" />
                </a>
                <div class="right d-flex align-items-center">
                    <a class="back" href="historyName.php">← <?= $lang == 'kz' ? 'Артқа' : ($lang == 'en' ? 'Back' : 'Назад') ?></a>
                    <span class="theme-toggle" onclick="toggleTheme()">🌓</span>
                </div>
            </div>
        </section>

        <!-- Переключение языков -->
        <div class="language-switcher">
            <a href="?lang=ru" class="<?= $lang == 'ru' ? 'active' : '' ?>">Русский</a>
            <a href="?lang=kz" class="<?= $lang == 'kz' ? 'active' : '' ?>">Қазақша</a>
            <a href="?lang=en" class="<?= $lang == 'en' ? 'active' : '' ?>">English</a>
        </div>

        <section class="HistoryPeople">
            <div class="history__container">
                <div class="historyPeople__title">
                    <?= $lang == 'kz' ? 'Колледж ардагерлері' : ($lang == 'en' ? 'College Veterans' : 'Ветераны колледжа') ?>
                </div>

                <div class="humansGrid">
                    <?php while ($veteran = mysqli_fetch_assoc($result_veterans)): ?>
                        <div class="veteranCard">
                            <img src="../admin/veterans/assets/images/<?= htmlspecialchars($veteran['photo']) ?>" alt="Фото ветерана">
                            <h3><?= htmlspecialchars($veteran['name_' . $lang] ?? $veteran['name_ru']) ?></h3>
                            <a href="../details/veteran_detail.php?id=<?= $veteran['id'] ?>&lang=<?= $lang ?>" class="detailsButton">
                                <?= $lang == 'kz' ? 'Толығырақ' : ($lang == 'en' ? 'Details' : 'Подробнее') ?>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>

            </div>
        </section>
    </div>
</body>

</html>