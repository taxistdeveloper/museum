<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$result_projects = mysqli_query($conn, "SELECT * FROM projects ORDER BY created_at DESC");
$museum_page_title = $lang['projects'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['projects']) ?></h1>
<div class="museum-grid">
    <?php while ($project = mysqli_fetch_assoc($result_projects)): ?>
        <a class="museum-card museum-card--center" href="../details/project_details.php?id=<?= (int)$project['id'] ?>&lang=<?= urlencode($language) ?>">
            <i class="bi bi-journal-bookmark-fill card-icon"></i>
            <div class="title"><?= htmlspecialchars($project['name']) ?></div>
        </a>
    <?php endwhile; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
