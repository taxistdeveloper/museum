<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$result_teachers = mysqli_query($conn, "SELECT * FROM teachers ORDER BY created_at DESC");
$museum_page_title = $lang['teachers_list'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['teachers_list']) ?></h1>
<div class="museum-grid">
    <?php while ($teacher = mysqli_fetch_assoc($result_teachers)): ?>
        <article class="museum-card">
            <h3><?= htmlspecialchars($teacher['name']) ?></h3>
            <p><?= nl2br(htmlspecialchars(mb_strimwidth((string)$teacher['description'], 0, 180, '...'))) ?></p>
            <a href="../details/teacher_detail.php?id=<?= (int)$teacher['id'] ?>&lang=<?= urlencode($language) ?>" class="museum-btn">
                <?= htmlspecialchars($lang['more']) ?>
            </a>
        </article>
    <?php endwhile; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
