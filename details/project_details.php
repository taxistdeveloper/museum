<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$project_id = (int)($_GET['id'] ?? 0);
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
$stmt->bind_param("is", $project_id, $db_lang);
$stmt->execute();
$project_lang = $stmt->get_result()->fetch_assoc();
$stmt->close();

$name = $project_lang['name'] ?? $project['name'];
$description = $project_lang['description'] ?? $project['description'];
$gallery = array_filter(array_map('trim', explode(",", (string)$project['gallery'])));

$museum_page_title = $name;
$museum_back_href = '../pages/project.php?lang=' . urlencode($language);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<article class="project-box">
    <h1><?= htmlspecialchars($name) ?></h1>
    <h4><?= htmlspecialchars($lang['project_description']) ?></h4>
    <p class="description collapsed"><?= nl2br(htmlspecialchars((string)$description)) ?></p>
    <button type="button" class="toggle-description museum-btn museum-btn--ghost"><?= htmlspecialchars($lang['show_more']) ?></button>

    <?php if (!empty($gallery)): ?>
        <div class="gallery-grid" style="margin-top: 1.5rem;">
            <?php foreach ($gallery as $index => $image): ?>
                <img class="fullscreen-img"
                     src="../admin/projects/assets/images/<?= rawurlencode($image) ?>"
                     data-index="<?= (int)$index ?>"
                     alt="<?= htmlspecialchars($name) ?>"
                     loading="lazy"
                     style="width:100%;height:180px;object-fit:cover;border-radius:12px;cursor:pointer;">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</article>

<div id="lightbox-modal" class="lightbox-modal">
    <span class="lightbox-close">&times;</span>
    <span class="lightbox-prev">&#10094;</span>
    <img class="lightbox-content" id="lightbox-img" src="" alt="">
    <span class="lightbox-next">&#10095;</span>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const desc = document.querySelector(".description");
        const toggleBtn = document.querySelector(".toggle-description");
        const showMore = <?= json_encode($lang['show_more']) ?>;
        const hide = <?= json_encode($lang['hide']) ?>;
        toggleBtn.addEventListener("click", function () {
            desc.classList.toggle("collapsed");
            toggleBtn.textContent = desc.classList.contains("collapsed") ? showMore : hide;
        });

        const modal = document.getElementById("lightbox-modal");
        const modalImg = document.getElementById("lightbox-img");
        const images = Array.from(document.querySelectorAll(".fullscreen-img"));
        let currentIndex = 0;

        function showImage(index) {
            if (index >= 0 && index < images.length) {
                modalImg.src = images[index].src;
                currentIndex = index;
                modal.style.display = "block";
            }
        }
        images.forEach(function (img, idx) {
            img.addEventListener("click", function () { showImage(idx); });
        });
        document.querySelector(".lightbox-close").addEventListener("click", function () { modal.style.display = "none"; });
        modal.addEventListener("click", function (e) { if (e.target === modal) modal.style.display = "none"; });
        document.querySelector(".lightbox-prev").addEventListener("click", function () {
            showImage((currentIndex - 1 + images.length) % images.length);
        });
        document.querySelector(".lightbox-next").addEventListener("click", function () {
            showImage((currentIndex + 1) % images.length);
        });
        document.addEventListener("keydown", function (e) {
            if (modal.style.display === "block") {
                if (e.key === "Escape") modal.style.display = "none";
                if (e.key === "ArrowLeft") showImage((currentIndex - 1 + images.length) % images.length);
                if (e.key === "ArrowRight") showImage((currentIndex + 1) % images.length);
            }
        });
    });
</script>
<style>
    .description.collapsed { max-height: 160px; overflow: hidden; position: relative; }
    .description.collapsed::after {
        content: "";
        position: absolute;
        bottom: 0; left: 0;
        height: 40px; width: 100%;
        background: linear-gradient(to bottom, transparent, var(--card-bg));
    }
    .toggle-description { margin-top: 10px; }
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
