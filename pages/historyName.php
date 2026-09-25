<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';
$museum_page_title = $lang['history_name'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['history_name']) ?></h1>
<div class="museum-grid">
    <a href="directors.php?lang=<?= urlencode($language) ?>" class="museum-card museum-card--center">
        <div class="icon-circle"><i class="bi bi-person-fill"></i></div>
        <div class="title"><?= htmlspecialchars($lang['directors']) ?></div>
    </a>
    <a href="veteran.php?lang=<?= urlencode($language) ?>" class="museum-card museum-card--center">
        <div class="icon-circle"><i class="bi bi-award-fill"></i></div>
        <div class="title"><?= htmlspecialchars($lang['veterans']) ?></div>
    </a>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
