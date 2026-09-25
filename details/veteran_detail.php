<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$veteran_id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM veterans WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $veteran_id);
mysqli_stmt_execute($stmt);
$veteran = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$veteran) {
    die("<h2 style='text-align:center;margin-top:3rem;'>Ветеран не найден</h2>");
}

$name = $veteran['name_' . $db_lang] ?? $veteran['name_ru'];
$biography = htmlspecialchars($veteran['biography_' . $db_lang] ?? $veteran['biography_ru']);
$gallery = !empty($veteran['gallery']) ? explode(",", $veteran['gallery']) : [];
$short_bio = (mb_strlen($biography) > 300) ? mb_substr($biography, 0, 1000) . '...' : $biography;

$museum_page_title = $name;
$museum_back_href = '../pages/veteran.php?lang=' . urlencode($language);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<article class="person-card" style="max-width:720px;margin:0 auto;padding:2rem;">
    <img src="../admin/veterans/assets/images/<?= htmlspecialchars($veteran['photo']) ?>"
         alt="<?= htmlspecialchars($name) ?>"
         style="width:220px;height:220px;border-radius:50%;object-fit:cover;border:4px solid var(--primary);">
    <h1><?= htmlspecialchars($name) ?></h1>
    <p><?= nl2br($short_bio) ?></p>
    <?php if (mb_strlen($biography) > 300): ?>
        <button type="button" class="museum-btn museum-btn--ghost" onclick="document.getElementById('bioModal').classList.add('active')">
            <?= htmlspecialchars($lang['read_more']) ?>
        </button>
    <?php endif; ?>
</article>

<?php if (!empty($gallery)): ?>
    <h2 class="museum-title"><?= htmlspecialchars($lang['gallery']) ?></h2>
    <div class="gallery-grid">
        <?php foreach ($gallery as $img): ?>
            <img src="../admin/veterans/assets/images/<?= htmlspecialchars(trim($img)) ?>"
                 alt="<?= htmlspecialchars($name) ?>"
                 loading="lazy"
                 style="width:100%;height:180px;object-fit:cover;border-radius:12px;cursor:pointer;"
                 onclick="openLightbox(this.src)">
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="museum-modal" id="bioModal" onclick="if(event.target===this)this.classList.remove('active')">
    <div class="museum-modal__card">
        <div class="museum-modal__head">
            <h4><?= htmlspecialchars($lang['biography']) ?></h4>
            <button class="museum-modal__close" type="button" onclick="document.getElementById('bioModal').classList.remove('active')">×</button>
        </div>
        <p><?= nl2br($biography) ?></p>
    </div>
</div>

<div class="lightbox-modal" id="lightbox" onclick="this.style.display='none'">
    <img class="lightbox-content" id="lightbox-img" src="" alt="">
</div>

<script>
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox').style.display = 'block';
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.getElementById('bioModal').classList.remove('active');
            document.getElementById('lightbox').style.display = 'none';
        }
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
