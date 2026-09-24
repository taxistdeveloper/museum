<?php
require '../config.php';

$lang = $_GET['lang'] ?? 'ru';

$query = "SELECT s.*, sl.name AS translated_name, sl.description AS translated_description 
          FROM specialties s
          LEFT JOIN specialties_lang sl ON s.id = sl.specialty_id AND sl.language = '$lang'
          ORDER BY s.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">

<head>
    <meta charset="UTF-8" />
    <title>История специальностей</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-color: #212529;
            --muted-color: #6c757d;
            --title-color: #0d6efd;
            --shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        }

        body.dark {
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #e4e4e4;
            --muted-color: #b0b0b0;
            --title-color: #91caff;
            --shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Montserrat', sans-serif;
            transition: background 0.3s, color 0.3s;
        }

        .language-switcher {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin: 20px 0;
        }

        .language-switcher a {
            padding: 8px 20px;
            border-radius: 30px;
            background: var(--card-bg);
            color: var(--text-color);
            border: 1px solid #ccc;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .language-switcher a.active,
        .language-switcher a:hover {
            background-color: var(--title-color);
            color: #fff;
            border-color: var(--title-color);
        }

        .page-header {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 10px;
            font-size: 2.2rem;
            font-weight: 700;
        }

        .back-link {
            text-align: center;
            margin-bottom: 15px;
        }

        .back-link a {
            text-decoration: none;
            color: var(--title-color);
            font-weight: 500;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .card-specialty {
            border: none;
            border-radius: 16px;
            transition: 0.3s ease;
            background: var(--card-bg);
            box-shadow: var(--shadow);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-specialty:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--title-color);
        }

        .card-text {
            color: var(--text-color);
            line-height: 1.6;
            font-size: 0.95rem;
        }

        .card-icon-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 1.6rem;
            color: var(--muted-color);
        }

        .footer-note {
            text-align: center;
            margin-top: 50px;
            font-size: 0.9rem;
            color: var(--muted-color);
        }

        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="container py-4 position-relative">
        <span class="theme-toggle" onclick="toggleTheme()">🌓</span>

        <div class="back-link">
            <a href="../index.php"><i class="bi bi-arrow-left-circle"></i> Назад</a>
        </div>

        <div class="language-switcher">
            <a href="?lang=ru" class="<?= $lang == 'ru' ? 'active' : '' ?>">Русский</a>
            <a href="?lang=kz" class="<?= $lang == 'kz' ? 'active' : '' ?>">Қазақша</a>
            <a href="?lang=en" class="<?= $lang == 'en' ? 'active' : '' ?>">English</a>
        </div>

        <h1 class="page-header">
            <i class="bi bi-journal-text"></i> История развития специальностей
        </h1>

        <div class="row g-4 mt-3">
            <?php while ($specialty = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-specialty p-3">
                        <div>
                            <h5 class="card-title">
                                <i class="bi bi-book-fill"></i>
                                <?= htmlspecialchars($specialty['translated_name'] ?? $specialty['name']) ?>
                            </h5>
                            <p class="card-text mt-2">
                                <i class="bi bi-card-text"></i>
                                <?= nl2br(htmlspecialchars($specialty['translated_description'] ?? $specialty['description'])) ?>
                            </p>
                        </div>
                        <div class="card-icon-footer">
                            <i class="bi <?= htmlspecialchars($specialty['icon'] ?? 'bi-book') ?>"></i>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="footer-note">&copy; <?= date('Y') ?> KTSK College – Все права защищены</div>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark');
        }
    </script>

</body>

</html>