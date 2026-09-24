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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background: #fdfdfc;
            font-family: 'Segoe UI', sans-serif;
            color: #2b2b2b;
        }

        .veteran-card {
            background: white;
            border: 2px solid #e5e5e5;
            border-radius: 20px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
            padding: 40px;
            margin-top: 40px;
        }

        .veteran-photo {
            width: 100%;
            max-width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius: 16px;
            border: 4px solid #0077b6;
        }

        h1 {
            font-size: 2.4rem;
            font-weight: 700;
            color: #0a4a8f;
        }

        .gold-line {
            width: 60px;
            height: 4px;
            background: #d4af37;
            margin: 15px 0;
            border-radius: 4px;
        }

        .bio-text {
            white-space: pre-line;
            text-align: justify;
            line-height: 1.9;
            text-indent: 2em;
            font-size: 1.2rem;
        }

        .btn-back {
            background: #0a4a8f;
            color: white;
            padding: 10px 24px;
            border-radius: 30px;
            font-weight: 500;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #06346c;
        }

        .lang-switcher {
            margin-top: 20px;
        }

        .gallery-img {
            border-radius: 12px;
            height: 180px;
            object-fit: cover;
            transition: 0.3s ease;
            border: 2px solid #e0e0e0;
        }

        .gallery-img:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .modal-body img {
            width: 100%;
            border-radius: 8px;
        }

        .section-title {
            text-align: center;
            margin: 60px 0 30px;
            font-size: 1.8rem;
            color: #0a4a8f;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- Language Switch -->
        <div class="d-flex justify-content-end lang-switcher">
            <div class="btn-group">
                <a href="?id=<?= $veteran_id ?>&lang=ru" class="btn btn-outline-primary <?= $lang == 'ru' ? 'active' : '' ?>">🇷🇺 Рус</a>
                <a href="?id=<?= $veteran_id ?>&lang=kz" class="btn btn-outline-primary <?= $lang == 'kz' ? 'active' : '' ?>">🇰🇿 Қазақ</a>
                <a href="?id=<?= $veteran_id ?>&lang=en" class="btn btn-outline-primary <?= $lang == 'en' ? 'active' : '' ?>">🇬🇧 Eng</a>
            </div>
        </div>

        <!-- Ветеран -->
        <div class="veteran-card text-center">
            <img src="../admin/veterans/assets/images/<?= htmlspecialchars($veteran['photo']) ?>" alt="Фото ветерана" class="veteran-photo mb-4">
            <h1><?= htmlspecialchars($name) ?></h1>
            <div class="gold-line mx-auto"></div>
            <div class="bio-text mt-4">
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

        <!-- Галерея -->
        <?php if (!empty($gallery)): ?>
            <div class="section-title"><?= $lang == 'kz' ? 'Галерея' : ($lang == 'en' ? 'Gallery' : 'Галерея') ?></div>
            <div class="row g-4">
                <?php foreach ($gallery as $img): ?>
                    <div class="col-sm-6 col-md-4">
                        <img src="../admin/veterans/assets/images/<?= htmlspecialchars(trim($img)) ?>" class="gallery-img w-100" data-bs-toggle="modal" data-bs-target="#imgModal<?= md5($img) ?>" alt="Gallery Image">
                    </div>

                    <!-- Модалка -->
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

        <!-- Модалка полной биографии -->
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