<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';
$museum_page_title = $lang['photo'];
$museum_back_href = 'gallery_video.php?lang=' . urlencode($language);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['photo']) ?></h1>
<script src="https://static.elfsight.com/platform/platform.js" async></script>
<div class="elfsight-app-e0cb7e81-aedb-45fe-9615-c777dbe6e3db" data-elfsight-app-lazy></div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
