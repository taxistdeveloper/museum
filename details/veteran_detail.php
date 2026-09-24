<?php
require '../config.php';

$veteran_id = $_GET['id'] ?? 0;
$lang = $_GET['lang'] ?? 'ru';

$stmt = mysqli_prepare($conn, "SELECT * FROM veterans WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $veteran_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$veteran = mysqli_fetch_assoc($result);

if (!$veteran) {
    die("<h2 class='text-center mt-5'>Ветеран табылмады</h2>");
}

$name_field = "name_" . $lang;
$bio_field = "biography_" . $lang;
$name = $veteran[$name_field] ?? $veteran['name_ru'];
$biography = htmlspecialchars($veteran[$bio_field] ?? $veteran['biography_ru']);
$gallery = !empty($veteran['gallery']) ? explode(",", $veteran['gallery']) : [];
$short_bio = (mb_strlen($biography) > 300) ? mb_substr($biography, 0, 1000) . '...' : $biography;
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($name) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

        .veteran-card {
            background-color: var(--card-bg);
            box-shadow: var(--shadow);
            border-radius: var(--radius);
            padding: 2rem;
            text-align: center;
            margin-top: 2rem;
        }

        .veteran-photo {
            width: 100%;
            max-width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius: var(--radius);
            border: 4px solid var(--primary);
            margin-bottom: 1rem;
        }

        h1 {
            color: var(--primary);
            font-size: 2rem;
            font-weight: 700;
        }

        .gold-line {
            width: 60px;
            height: 4px;
            background: #d4af37;
            margin: 15px auto;
            border-radius: 4px;
        }

        .bio-text {
            white-space: pre-line;
            text-align: justify;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-top: 1rem;
        }

        .btn-back {
            background-color: var(--primary);
            color: #fff;
            padding: 10px 24px;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 500;
        }

        .btn-back:hover {
            background-color: #0b5ed7;
        }

        .lang-switcher {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .lang-switcher a {
            padding: 6px 14px;
            border-radius: 999px;
            background: var(--card-bg);
            color: var(--text-color);
            border: 1px solid #ccc;
            text-decoration: none;
        }

        .lang-switcher a.active,
        .lang-switcher a:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .gallery-img {
            border-radius: var(--radius);
            height: 180px;
            object-fit: cover;
            transition: 0.3s;
            border: 2px solid #e0e0e0;
            box-shadow: var(--shadow);
        }

        .gallery-img:hover {
            transform: scale(1.03);
        }

        .section-title {
            text-align: center;
            margin: 60px 0 30px;
            font-size: 1.8rem;
            color: var(--primary);
            font-weight: 600;
        }

        .theme-toggle {
            font-size: 1.4rem;
            cursor: pointer;
            margin-left: 15px;
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
        <!-- Language & Theme -->
        <div class="d-flex justify-content-between align-items-center">
            <div class="lang-switcher">
                <a href="?id=<?= $veteran_id ?>&lang=ru" class="<?= $lang == 'ru' ? 'active' : '' ?>">🇷🇺 Рус</a>
                <a href="?id=<?= $veteran_id ?>&lang=kz" class="<?= $lang == 'kz' ? 'active' : '' ?>">🇰🇿 Қазақ</a>
                <a href="?id=<?= $veteran_id ?>&lang=en" class="<?= $lang == 'en' ? 'active' : '' ?>">🇬🇧 Eng</a>
            </div>
            <div class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">🌓</div>
        </div>

        <!-- Veteran -->
        <div class="veteran-card">
            <img src="../admin/veterans/assets/images/<?= htmlspecialchars($veteran['photo']) ?>" alt="Фото ветерана" class="veteran-photo">
            <h1><?= htmlspecialchars($name) ?></h1>
            <div class="gold-line"></div>
            <div class="bio-text">
                <?= nl2br($short_bio) ?>
                <?php if (mb_strlen($biography) > 300): ?>
                    <div class="mt-2">
                        <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#bioModal">
                            <?= $lang == 'kz' ? 'Толық оқу' : ($lang == 'en' ? 'Read more' : 'Читать полностью') ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <a href="../pages/veteran.php?lang=<?= $lang ?>" class="btn-back mt-4">← <?= $lang == 'kz' ? 'Артқа' : ($lang == 'en' ? 'Back' : 'Назад') ?></a>
        </div>

        <!-- Gallery -->
        <?php if (!empty($gallery)): ?>
            <div class="section-title"><?= $lang == 'kz' ? 'Галерея' : ($lang == 'en' ? 'Gallery' : 'Галерея') ?></div>
            <div class="row g-4">
                <?php foreach ($gallery as $img): ?>
                    <div class="col-sm-6 col-md-4">
                        <img src="../admin/veterans/assets/images/<?= htmlspecialchars(trim($img)) ?>" class="gallery-img w-100" data-bs-toggle="modal" data-bs-target="#imgModal<?= md5($img) ?>" alt="Gallery Image">
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="imgModal<?= md5($img) ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <div class="modal-body p-0">
                                    <img src="../admin/veterans/assets/images/<?= htmlspecialchars(trim($img)) ?>" alt="Full Image">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Full Biography Modal -->
        <div class="modal fade" id="bioModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><?= ($lang == 'kz' ? 'Өмірбаяны' : ($lang == 'en' ? 'Biography' : 'Биография')) ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="bio-text"><?= nl2br($biography) ?></p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>

</html>