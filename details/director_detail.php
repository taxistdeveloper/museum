<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$director_id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM directors WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $director_id);
mysqli_stmt_execute($stmt);
$director = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$director) {
    die("<h2 style='text-align:center;margin-top:3rem;'>Директор не найден</h2>");
}

$name = $director['name_' . $db_lang] ?? $director['name_ru'];
$biography = htmlspecialchars($director['biography_' . $db_lang] ?? $director['biography_ru']);
$gallery = $director['gallery'] ? explode(",", $director['gallery']) : [];
$short_bio = (mb_strlen($biography) > 300) ? mb_substr($biography, 0, 300) . '...' : $biography;

$museum_page_title = $name;
$museum_back_href = '../pages/directors.php?lang=' . urlencode($language);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<article class="profile-card">
    <div class="profile-card__row">
        <img src="../admin/directors/assets/images/<?= htmlspecialchars($director['photo']) ?>"
             alt="<?= htmlspecialchars($name) ?>"
             style="width:220px;height:220px;object-fit:cover;border-radius:12px;">
        <div>
            <h1><?= htmlspecialchars($name) ?></h1>
            <p><strong><?= htmlspecialchars($lang['description']) ?>:</strong></p>
            <p><?= nl2br($short_bio) ?>
                <?php if (mb_strlen($biography) > 300): ?>
                    <button type="button" class="museum-btn museum-btn--ghost" onclick="document.getElementById('bioModal').classList.add('active')">
                        <?= htmlspecialchars($lang['read_more']) ?>
                    </button>
                <?php endif; ?>
            </p>
        </div>
    </div>
</article>

<?php if (!empty($gallery)): ?>
    <h2 class="museum-title"><?= htmlspecialchars($lang['gallery']) ?></h2>
    <div class="gallery-grid">
        <?php foreach ($gallery as $image): ?>
            <img src="../admin/directors/assets/images/<?= htmlspecialchars(trim($image)) ?>"
                 alt="<?= htmlspecialchars($name) ?>"
                 loading="lazy"
                 style="width:100%;height:220px;object-fit:cover;border-radius:12px;">
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="empty-state"><?= htmlspecialchars($lang['gallery_empty']) ?></p>
<?php endif; ?>

<div class="museum-modal" id="bioModal" onclick="if(event.target===this)this.classList.remove('active')">
    <div class="museum-modal__card">
        <div class="museum-modal__head">
            <h4><?= htmlspecialchars($lang['biography']) ?>: <?= htmlspecialchars($name) ?></h4>
            <button class="museum-modal__close" type="button" onclick="document.getElementById('bioModal').classList.remove('active')">×</button>
        </div>
        <p><?= nl2br($biography) ?></p>
    </div>
</div>

<script>
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') document.getElementById('bioModal').classList.remove('active');
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
