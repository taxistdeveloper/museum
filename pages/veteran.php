<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$col = in_array($db_lang, ['ru', 'kz', 'en'], true) ? $db_lang : 'ru';
$result_veterans = mysqli_query($conn, "SELECT * FROM veterans ORDER BY LOWER(COALESCE(name_$col, name_ru)) ASC");
$museum_page_title = $lang['veterantitle'];
$museum_back_href = 'historyName.php?lang=' . urlencode($language);
$name_key = 'name_' . $col;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['veterantitle']) ?></h1>
<div class="person-grid">
    <?php while ($veteran = mysqli_fetch_assoc($result_veterans)): ?>
        <article class="person-card">
            <img src="../admin/veterans/assets/images/<?= htmlspecialchars($veteran['photo']) ?>"
                 alt="<?= htmlspecialchars($veteran[$name_key] ?? $veteran['name_ru']) ?>"
                 loading="lazy">
            <h3><?= htmlspecialchars($veteran[$name_key] ?? $veteran['name_ru']) ?></h3>
            <a href="../details/veteran_detail.php?id=<?= (int)$veteran['id'] ?>&lang=<?= urlencode($language) ?>" class="museum-btn">
                <?= htmlspecialchars($lang['more']) ?>
            </a>
        </article>
    <?php endwhile; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
