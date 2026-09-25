<?php
require '../config.php';

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';

$query_directors = "SELECT * FROM directors ORDER BY created_at DESC";
$result_directors = mysqli_query($conn, $query_directors);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Директора учебного заведения</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
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

        .language-switcher {
            display: flex;
            gap: 10px;
            justify-content: center;
            padding: 1rem 0;
            flex-wrap: wrap;
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

        .historyPeople__title {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 2rem 0 1.5rem;
            text-align: center;
        }

        .humansGrid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .HumanCard {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .HumanCard .image img {
            max-width: 100%;
            border-radius: var(--radius);
            height: auto;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .historyPeople__name {
            margin-top: 1rem;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .historyPeople__button {
            margin-top: 1rem;
        }

        .historyPeople__button a {
            background-color: var(--primary);
            color: #fff;
            padding: 8px 16px;
            border-radius: var(--radius);
            text-decoration: none;
            transition: 0.3s;
        }

        .historyPeople__button a:hover {
            background-color: #0056c7;
        }

        .theme-toggle {
            cursor: pointer;
            font-size: 1.5rem;
            margin-left: 1rem;
        }

        @media (max-width: 768px) {
            .header__container {
                flex-direction: column;
                align-items: center;
            }

            .theme-toggle {
                margin-top: 1rem;
            }
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
                <a class="logo" href="">
                    <img src="../assets/img/logo.png" alt="Логотип" />
                </a>
                <div class="right">
                    <a class="back" href="historyName.php">Назад</a>
                    <span class="theme-toggle" onclick="toggleTheme()" title="Переключить тему">🌓</span>
                </div>
            </div>
        </section>

        <!-- Переключение языков -->
        <div class="language-switcher">
            <a href="?lang=kz" class="<?= $lang == 'kz' ? 'active' : '' ?>">Қазақша</a>
            <a href="?lang=ru" class="<?= $lang == 'ru' ? 'active' : '' ?>">Русский</a>
            <a href="?lang=en" class="<?= $lang == 'en' ? 'active' : '' ?>">English</a>
        </div>

        <section class="HistoryPeople">
            <div class="history__container">
                <div class="historyPeople__title">
                    <?php
                    echo ($lang == 'kz') ? "Оқу орны директорлары" : (($lang == 'en') ? "Directors of the Educational Institution" : "Директора учебного заведения");
                    ?>
                </div>

                <div class="humansGrid">
                    <?php while ($director = mysqli_fetch_assoc($result_directors)): ?>
                        <div class="HumanCard">
                            <div class="image">
                                <img src="../admin/directors/assets/images/<?= $director['photo'] ?>" alt="Фото директора">
                            </div>
                            <div class="historyPeople__name">
                                <?= htmlspecialchars($director['name_' . $lang] ?? $director['name_ru']) ?>
                            </div>
                            <div class="historyPeople__button">
                                <a href="../details/director_detail.php?id=<?= $director['id'] ?>&lang=<?= $lang ?>">Подробнее</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    </div>
</body>

</html>