<?php
include 'config.php';
include 'includes/init.php';
$museum_show_back = false;
$museum_page_title = $lang['site_title'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/header.php'; ?>

<div class="museum-grid">
    <a class="museum-card museum-card--center" href="pages/one.php?lang=<?= urlencode($language) ?>">
        <i class="bi bi-megaphone card-icon"></i>
        <p class="title"><?= $lang['title']; ?></p>
    </a>
    <a class="museum-card museum-card--center" href="pages/history.php?lang=<?= urlencode($language) ?>">
        <i class="bi bi-clock-history card-icon"></i>
        <p class="title"><?= $lang['history']; ?></p>
    </a>
    <a class="museum-card museum-card--center" href="pages/historyName.php?lang=<?= urlencode($language) ?>">
        <i class="bi bi-people card-icon"></i>
        <p class="title"><?= $lang['history_name']; ?></p>
    </a>
    <a class="museum-card museum-card--center" href="pages/project.php?lang=<?= urlencode($language) ?>">
        <i class="bi bi-diagram-3 card-icon"></i>
        <p class="title"><?= $lang['projects']; ?></p>
    </a>
    <a class="museum-card museum-card--center" href="pages/speciality.php?lang=<?= urlencode($language) ?>">
        <i class="bi bi-award card-icon"></i>
        <p class="title"><?= $lang['specialties']; ?></p>
    </a>
    <a class="museum-card museum-card--center" href="pages/teacher.php?lang=<?= urlencode($language) ?>">
        <i class="bi bi-person-badge-fill card-icon"></i>
        <p class="title"><?= $lang['teachers']; ?></p>
    </a>
    <a class="museum-card museum-card--center" href="pages/student.php?lang=<?= urlencode($language) ?>">
        <i class="bi bi-person-lines-fill card-icon"></i>
        <p class="title"><?= $lang['students']; ?></p>
    </a>
    <a class="museum-card museum-card--center" href="pages/gallery_video.php?lang=<?= urlencode($language) ?>">
        <i class="bi bi-camera-reels-fill card-icon"></i>
        <p class="title"><?= $lang['videos']; ?></p>
    </a>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
