<?php
require '../config.php';

$director_id = $_GET['id'] ?? 0;
$lang = $_GET['lang'] ?? 'ru';

$stmt = mysqli_prepare($conn, "SELECT * FROM directors WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $director_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$director = mysqli_fetch_assoc($result);

if (!$director) {
    die("<h2 class='text-center mt-5'>Директор не найден</h2>");
}

$name_field = "name_" . $lang;
$bio_field = "biography_" . $lang;
$name = $director[$name_field] ?? $director['name_ru'];
$biography = htmlspecialchars($director[$bio_field] ?? $director['biography_ru']);
$gallery = $director['gallery'] ? explode(",", $director['gallery']) : [];
$short_bio = (mb_strlen($biography) > 300) ? mb_substr($biography, 0, 300) . '...' : $biography;
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .language-switcher {
            display: flex;
            gap: 10px;
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

        .card-style {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .gallery-grid img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: var(--radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
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
        <div class="header">
            <div class="language-switcher">
                <a href="?id=<?= $director_id ?>&lang=ru" class="<?= $lang == 'ru' ? 'active' : '' ?>">🇷🇺 Рус</a>
                <a href="?id=<?= $director_id ?>&lang=kz" class="<?= $lang == 'kz' ? 'active' : '' ?>">🇰🇿 Қазақ</a>
                <a href="?id=<?= $director_id ?>&lang=en" class="<?= $lang == 'en' ? 'active' : '' ?>">🇬🇧 Eng</a>
            </div>
            <span class="theme-toggle" onclick="toggleTheme()">🌓</span>
        </div>

        <div class="card-style mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-md-4 text-center">
                    <img src="../admin/directors/assets/images/<?= htmlspecialchars($director['photo']) ?>" alt="Фото: <?= htmlspecialchars($name) ?>" class="img-fluid rounded shadow">
                </div>
                <div class="col-md-8">
                    <h2 class="mb-3"><?= htmlspecialchars($name) ?></h2>
                    <p><strong><?= $lang == 'kz' ? 'Сипаттамасы:' : ($lang == 'en' ? 'Description:' : 'Описание:') ?></strong></p>
                    <p><?= nl2br($short_bio) ?>
                        <?php if (mb_strlen($biography) > 300): ?>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#bioModal" class="text-primary">
                                <?= $lang == 'kz' ? 'Толық оқу' : ($lang == 'en' ? 'Read more' : 'Читать полностью') ?>
                            </a>
                        <?php endif; ?>
                    </p>
                    <a href="../pages/directors.php?lang=<?= $lang ?>" class="back mt-3 d-inline-block">← <?= $lang == 'kz' ? 'Артқа' : ($lang == 'en' ? 'Back' : 'Назад') ?></a>
                </div>
            </div>
        </div>

        <?php if (!empty($gallery)) : ?>
            <h3 class="text-center"><?= $lang == 'kz' ? 'Галерея' : ($lang == 'en' ? 'Gallery' : 'Галерея') ?></h3>
            <div class="gallery-grid">
                <?php foreach ($gallery as $image) : ?>
                    <img src="../admin/directors/assets/images/<?= htmlspecialchars(trim($image)) ?>" alt="Gallery image">
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="text-center text-muted mt-3">
                <?= $lang == 'kz' ? 'Галерея бос.' : ($lang == 'en' ? 'Gallery is empty.' : 'Галерея пуста.') ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Модалка -->
    <div class="modal fade" id="bioModal" tabindex="-1" aria-labelledby="bioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content card-style">
                <div class="modal-header">
                    <h5 class="modal-title" id="bioModalLabel">
                        <?= $lang == 'kz' ? 'Өмірбаяны: ' : ($lang == 'en' ? 'Biography: ' : 'Биография: ') ?>
                        <?= htmlspecialchars($name) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <p><?= nl2br($biography) ?></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>