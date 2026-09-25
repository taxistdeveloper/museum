<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';
$museum_page_title = $lang['pv_title'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['pv_title']) ?></h1>
<div class="museum-grid">
    <a href="./insta_blog.php?lang=<?= urlencode($language) ?>" class="museum-card museum-card--center">
        <i class="bi bi-camera-fill card-icon"></i>
        <div class="title"><?= htmlspecialchars($lang['photo']) ?></div>
    </a>
    <a href="../videos.php?lang=<?= urlencode($language) ?>" class="museum-card museum-card--center">
        <i class="bi bi-youtube card-icon"></i>
        <div class="title"><?= htmlspecialchars($lang['video']) ?></div>
    </a>
    <a href="./media_about_us.php?lang=<?= urlencode($language) ?>" class="museum-card museum-card--center">
        <i class="bi bi-newspaper card-icon"></i>
        <div class="title"><?= htmlspecialchars($lang['media_about_us']) ?></div>
    </a>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
