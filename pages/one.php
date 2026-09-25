<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';
$museum_page_title = $lang['one_title'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="oneBlock" style="margin-bottom: 1.5rem;">
    <?= $lang['one_paragraph']; ?>
</div>

<div class="twoBlock">
    <h1 class="museum-title" style="margin-top: 0;"><?= $lang['director_title']; ?></h1>
    <div class="twoBlockGrid">
        <img src="../assets/img/director.jpg" alt="<?= htmlspecialchars($lang['director_title']) ?>">
        <div class="text">
            <?= $lang['director_paragraph']; ?><br><br>
            <?= $lang['teaching']; ?><br><br>
            <?= $lang['developing']; ?><br><br>
            <?= $lang['inspiring']; ?><br><br>
            <?= $lang['college_vision']; ?><br><br>
            <?= $lang['mission']; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
