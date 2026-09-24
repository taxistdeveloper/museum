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
<html lang="<?= $language ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $lang['history_title'] ?? 'История колледжа в именах'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #007bff;
            --bg: #f9fafb;
            --text: #1f1f1f;
            --card-bg: #fff;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            --radius: 12px;
        }

        body.dark {
            --primary: #91caff;
            --bg: #121212;
            --text: #e0e0e0;
            --card-bg: #1e1e1e;
            --shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
        }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            transition: all 0.3s ease;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 1.5rem;
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
            color: var(--text);
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

        .back {
            background-color: var(--primary);
            color: #fff;
            padding: 8px 16px;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .back:hover {
            background-color: #0056b3;
        }

        .historyName__title {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 600;
            margin: 2rem 0 2.5rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            padding-bottom: 2rem;
        }

        .ContentBlock {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem;
            background-color: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            text-decoration: none;
            color: var(--text);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .ContentBlock:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .ContentBlock .icon {
            width: 60px;
            height: 60px;
            margin-bottom: 1rem;
            background-color: var(--primary);
            border-radius: 50%;
        }

        .ContentBlock .title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        @media (max-width: 600px) {
            .historyName__title {
                font-size: 1.4rem;
            }

            .ContentBlock {
                padding: 1.5rem;
            }
        }

        .bi {
            font-size: 45px;
            color: #ffff;
        }
    </style>
</head>

<body>
    <div class="container">
        <section class="header">
            <div class="header__container">
                <a class="logo" href="/">
                    <img src="https://ff2.object.pscloud.io/giwycqkw/oeqsaqye/eoamgqcg/4384bf6eeeace361ca86ed2a4e97d2975c665532.png" alt="Логотип" />
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
                <a class="back" href="../index.php"><?= $lang['back'] ?? 'Назад'; ?></a>
            </div>
        </section>

        <section class="HistoryName">
            <div class="historyName__container">
                <div class="historyName__title"><?= $lang['history_title'] ?? 'История колледжа в именах'; ?></div>
                <div class="grid">
                    <a href="directors.php?lang=<?= $language ?>" class="ContentBlock">
                        <div class="icon bi bi-person-fill"></div>
                        <div class="title"><?= $lang['directors'] ?? 'Директора учебного заведения'; ?></div>
                    </a>

                    <a href="veteran.php?lang=<?= $language ?>" class="ContentBlock">
                        <div class="icon bi bi-person-fill"></div>
                        <div class="title"><?= $lang['veterans'] ?? 'Педагоги-ветераны учебного заведения'; ?></div>
                    </a>
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