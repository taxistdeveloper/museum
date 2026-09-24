<?php
require '../config.php';

$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$lang = $_GET['lang'] ?? 'ru';

if ($project_id <= 0) {
    die("Ошибка: Некорректный ID проекта.");
}

$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$project = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$project) {
    die("Ошибка: Проект не найден.");
}

$stmt = $conn->prepare("SELECT * FROM projects_lang WHERE project_id = ? AND language = ?");
$stmt->bind_param("is", $project_id, $lang);
$stmt->execute();
$project_lang = $stmt->get_result()->fetch_assoc();
$stmt->close();

$name = $project_lang['name'] ?? $project['name'];
$description = $project_lang['description'] ?? $project['description'];
$gallery = array_filter(array_map('trim', explode(",", $project['gallery'])));
?>

<?php
require '../config.php';
$project_id = $_GET['id'] ?? 0;
$lang = $_GET['lang'] ?? 'ru';

// Получаем проект
$query_project = "SELECT * FROM projects WHERE id = $project_id LIMIT 1";
$result_project = mysqli_query($conn, $query_project);
$project = mysqli_fetch_assoc($result_project);

$name = $project['name'];
$description = $project['description'];

// Получаем галерею (если есть)
$gallery = [];
if (!empty($project['gallery'])) {
    $gallery = explode(",", $project['gallery']);
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($name) ?> | Детали проекта</title>
    <meta name="description" content="<?= htmlspecialchars(mb_strimwidth($description, 0, 150, '...')) ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            padding: 1rem;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .project-box {
            background-color: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2rem;
            margin-top: 2rem;
        }

        h1 {
            margin-bottom: 1rem;
        }

        .language-switcher {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 1rem 0;
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

        .btn-back {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            font-size: 15px;
            border-radius: var(--radius);
            text-decoration: none;
            margin-bottom: 1.5rem;
        }

        .btn-back:hover {
            background-color: #0056c7;
        }

        .description.collapsed {
            max-height: 160px;
            overflow: hidden;
            position: relative;
        }

        .description.collapsed::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            height: 40px;
            width: 100%;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0), var(--card-bg));
        }

        .toggle-description {
            background: none;
            border: none;
            color: var(--primary);
            font-weight: 500;
            cursor: pointer;
            margin-top: 10px;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 2rem;
        }

        .gallery img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: var(--radius);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .gallery img:hover {
            transform: scale(1.03);
        }

        .theme-toggle {
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
            margin-bottom: 1rem;
        }

        /* Lightbox */
        .lightbox-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .lightbox-content {
            display: block;
            margin: auto;
            max-width: 90%;
            max-height: 80%;
            border-radius: 8px;
            margin-top: 5%;
        }

        .lightbox-close,
        .lightbox-prev,
        .lightbox-next {
            position: absolute;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }

        .lightbox-close {
            top: 20px;
            right: 30px;
        }

        .lightbox-prev {
            top: 50%;
            left: 30px;
            transform: translateY(-50%);
        }

        .lightbox-next {
            top: 50%;
            right: 30px;
            transform: translateY(-50%);
        }

        @media (max-width: 600px) {
            .gallery {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <span class="theme-toggle" onclick="toggleTheme()">🌓</span>

        <div class="project-box">
            <h1><?= htmlspecialchars($name) ?></h1>

            <div class="language-switcher">
                <a href="?id=<?= $project_id ?>&lang=ru" class="<?= $lang == 'ru' ? 'active' : '' ?>">Русский</a>
                <a href="?id=<?= $project_id ?>&lang=kz" class="<?= $lang == 'kz' ? 'active' : '' ?>">Қазақша</a>
                <a href="?id=<?= $project_id ?>&lang=en" class="<?= $lang == 'en' ? 'active' : '' ?>">English</a>
            </div>

            <a href="../pages/project.php" class="btn-back">← Назад</a>

            <h4>Описание проекта</h4>
            <div class="description-wrapper">
                <p class="description collapsed"><?= $project_lang['description'] ?></p>
                <button class="toggle-description">Показать больше</button>
            </div>

            <?php if (!empty($gallery)): ?>
                <div class="gallery">
                    <?php foreach ($gallery as $index => $image): ?>
                        <img class="fullscreen-img" src="../admin/projects/assets/images/<?= rawurlencode($image) ?>" data-index="<?= $index ?>" alt="Project image">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Lightbox -->
    <div id="lightbox-modal" class="lightbox-modal">
        <span class="lightbox-close">&times;</span>
        <span class="lightbox-prev">&#10094;</span>
        <img class="lightbox-content" id="lightbox-img" src="" alt="Full image">
        <span class="lightbox-next">&#10095;</span>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark');
        }

        document.addEventListener("DOMContentLoaded", function() {
            const desc = document.querySelector(".description");
            const toggleBtn = document.querySelector(".toggle-description");

            toggleBtn.addEventListener("click", () => {
                desc.classList.toggle("collapsed");
                toggleBtn.textContent = desc.classList.contains("collapsed") ? "Показать больше" : "Скрыть";
            });

            const modal = document.getElementById("lightbox-modal");
            const modalImg = document.getElementById("lightbox-img");
            const closeBtn = document.querySelector(".lightbox-close");
            const prevBtn = document.querySelector(".lightbox-prev");
            const nextBtn = document.querySelector(".lightbox-next");
            const images = Array.from(document.querySelectorAll(".fullscreen-img"));
            let currentIndex = 0;

            function showImage(index) {
                if (index >= 0 && index < images.length) {
                    modalImg.src = images[index].src;
                    currentIndex = index;
                    modal.style.display = "block";
                }
            }

            images.forEach((img, idx) => {
                img.addEventListener("click", () => showImage(idx));
            });

            closeBtn.addEventListener("click", () => modal.style.display = "none");
            modal.addEventListener("click", e => {
                if (e.target === modal) modal.style.display = "none";
            });

            prevBtn.addEventListener("click", () => showImage((currentIndex - 1 + images.length) % images.length));
            nextBtn.addEventListener("click", () => showImage((currentIndex + 1) % images.length));

            document.addEventListener("keydown", e => {
                if (modal.style.display === "block") {
                    if (e.key === "Escape") modal.style.display = "none";
                    if (e.key === "ArrowLeft") showImage((currentIndex - 1 + images.length) % images.length);
                    if (e.key === "ArrowRight") showImage((currentIndex + 1) % images.length);
                }
            });
        });
    </script>

</body>

</html>