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
    die("<h2 class='text-center mt-5'>Ветеран не найден</h2>");
}

$name_field = "name_" . $lang;
$bio_field = "biography_" . $lang;
$name = $veteran[$name_field] ?? $veteran['name_ru'];
$biography = htmlspecialchars($veteran[$bio_field] ?? $veteran['biography_ru']);
$gallery = !empty($veteran['gallery']) ? explode(",", $veteran['gallery']) : [];
$short_bio = (mb_strlen($biography) > 400) ? mb_substr($biography, 0, 800) . '...' : $biography;
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
            background: #f5f7fa;
            font-family: 'Segoe UI', sans-serif;
            color: #333;
        }

        .container {
            max-width: 960px;
        }

        .lang-switcher {
            margin-top: 20px;
            text-align: right;
        }

        .btn-lang {
            font-size: 14px;
            padding: 6px 16px;
            border-radius: 20px;
            margin-left: 6px;
        }

        .title {
            font-size: 32px;
            font-weight: 600;
            text-align: center;
            margin: 30px 0;
            color: #1e3a8a;
        }

        .profile-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 40px;
        }

        .profile-img {
            width: 100%;
            max-width: 240px;
            border-radius: 12px;
            object-fit: cover;
        }

        .profile-name {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #0f172a;
        }

        .bio-text {
            font-size: 16px;
            text-align: justify;
            text-indent: 1.5em;
            white-space: pre-line;
        }

        .btn-back {
            background-color: #1e3a8a;
            color: #fff;
            font-size: 14px;
            border-radius: 30px;
            padding: 8px 20px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }

        .gallery-title {
            font-size: 22px;
            text-align: center;
            margin-bottom: 20px;
            color: #1e3a8a;
        }

        .gallery-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
            transition: 0.3s ease;
        }

        .gallery-img:hover {
            transform: scale(1.02);
        }

        .modal-body img {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- Языки -->
        <div class="lang-switcher">
            <a href="?id=<?= $veteran_id ?>&lang=ru" class="btn btn-outline-primary btn-lang <?= $lang == 'ru' ? 'active' : '' ?>">🇷🇺 Рус</a>
            <a href="?id=<?= $veteran_id ?>&lang=kz" class="btn btn-outline-primary btn-lang <?= $lang == 'kz' ? 'active' : '' ?>">🇰🇿 Қазақ</a>
            <a href="?id=<?= $veteran_id ?>&lang=en" class="btn btn-outline-primary btn-lang <?= $lang == 'en' ? 'active' : '' ?>">🇬🇧 Eng</a>
        </div>

        <!-- Имя -->
        <h1 class="title"><?= htmlspecialchars($name) ?></h1>

        <!-- Профиль -->
        <div class="profile-box row g-4 align-items-start">
            <div class="col-md-4 text-center">
                <img src="../admin/veterans/assets/images/<?= htmlspecialchars($veteran['photo']) ?>" class="profile-img" alt="Фото ветерана">
            </div>
            <div class="col-md-8">
                <div class="profile-name"><?= htmlspecialchars($name) ?></div>
                <div class="bio-text"><?= nl2br($short_bio) ?></div>
                <?php if (mb_strlen($biography) > 400): ?>
                    <a href="#" class="text-primary d-block mt-2" data-bs-toggle="modal" data-bs-target="#bioModal">
                        <?= $lang == 'kz' ? 'Толық оқу' : ($lang == 'en' ? 'Read more' : 'Читать полностью') ?>
                    </a>
                <?php endif; ?>
                <a href="../pages/veteran.php?lang=<?= $lang ?>" class="btn-back">← <?= $lang == 'kz' ? 'Артқа' : ($lang == 'en' ? 'Back' : 'Назад') ?></a>
            </div>
        </div>

        <!-- Галерея -->
        <?php if (!empty($gallery)): ?>
            <h3 class="gallery-title"><?= $lang == 'kz' ? 'Галерея' : ($lang == 'en' ? 'Gallery' : 'Галерея') ?></h3>
            <div class="row g-3 mb-5">
                <?php foreach ($gallery as $img): ?>
                    <div class="col-sm-6 col-md-4">
                        <img src="../admin/veterans/assets/images/<?= htmlspecialchars(trim($img)) ?>" class="gallery-img" data-bs-toggle="modal" data-bs-target="#imgModal<?= md5($img) ?>" alt="Gallery">
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

        <!-- Полная биография -->
        <div class="modal fade" id="bioModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><?= ($lang == 'kz' ? 'Өмірбаяны' : ($lang == 'en' ? 'Biography' : 'Биография')) ?>: <?= htmlspecialchars($name) ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p style="font-size: 16px; text-indent: 1.5em; text-align: justify; white-space: pre-line;"><?= nl2br($biography) ?></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>

</html>