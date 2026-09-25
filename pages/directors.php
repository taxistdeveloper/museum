<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$result_directors = mysqli_query($conn, "SELECT * FROM directors ORDER BY created_at DESC");
$museum_page_title = $lang['directors'];
$museum_back_href = 'historyName.php?lang=' . urlencode($language);
$name_key = 'name_' . $db_lang;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['directors']) ?></h1>
<p class="museum-lead"><?= htmlspecialchars($lang['hisdirectortext']) ?></p>
<div class="person-grid">
    <?php while ($director = mysqli_fetch_assoc($result_directors)): ?>
        <article class="person-card">
            <div class="image">
                <img src="../admin/directors/assets/images/<?= htmlspecialchars($director['photo']) ?>"
                     alt="<?= htmlspecialchars($director[$name_key] ?? $director['name_ru']) ?>"
                     loading="lazy">
            </div>
            <h3 class="name"><?= htmlspecialchars($director[$name_key] ?? $director['name_ru']) ?></h3>
            <a class="museum-btn" href="../details/director_detail.php?id=<?= (int)$director['id'] ?>&lang=<?= urlencode($language) ?>">
                <?= htmlspecialchars($lang['more']) ?>
            </a>
        </article>
    <?php endwhile; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
