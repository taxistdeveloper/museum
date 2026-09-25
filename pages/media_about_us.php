<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$result = mysqli_query($conn, "SELECT * FROM media_publications ORDER BY publication_date DESC");
$total_publications = mysqli_num_rows($result);
$unique_media = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT media_name FROM media_publications"));
$estimated_views = $total_publications * 3000;
mysqli_data_seek($result, 0);

$museum_page_title = $lang['media_about_us'];
$museum_back_href = 'gallery_video.php?lang=' . urlencode($language);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['media_about_us']) ?></h1>
<p class="museum-lead"><?= htmlspecialchars($lang['media_about_us_lead']) ?></p>

<div class="stats-grid">
    <div class="stat-item">
        <h3><?= (int)$total_publications ?></h3>
        <p><?= htmlspecialchars($lang['publications']) ?></p>
    </div>
    <div class="stat-item">
        <h3><?= (int)$unique_media ?></h3>
        <p><?= htmlspecialchars($lang['media_outlets']) ?></p>
    </div>
    <div class="stat-item">
        <h3><?= number_format($estimated_views) ?>+</h3>
        <p><?= htmlspecialchars($lang['views']) ?></p>
    </div>
</div>

<section class="media-grid">
    <?php if ($total_publications > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <?php
            $icon = 'bi-newspaper';
            $link_text = $lang['more'];
            switch ($row['media_type']) {
                case 'tv':
                    $icon = 'bi-tv';
                    break;
                case 'radio':
                    $icon = 'bi-megaphone';
                    break;
                case 'online':
                    $icon = 'bi-globe';
                    break;
                case 'magazine':
                    $icon = 'bi-book';
                    break;
            }
            ?>
            <article class="media-card">
                <div class="media-header">
                    <div class="media-icon"><i class="bi <?= $icon ?>"></i></div>
                    <div>
                        <h3><?= htmlspecialchars($row['media_name']) ?></h3>
                        <div style="color: var(--muted); font-size: 0.9rem;"><?= date('d.m.Y', strtotime($row['publication_date'])) ?></div>
                    </div>
                </div>
                <p><?= htmlspecialchars($row['description']) ?></p>
                <?php if (!empty($row['link'])): ?>
                    <a href="<?= htmlspecialchars($row['link']) ?>" class="museum-btn museum-btn--ghost" target="_blank" rel="noopener">
                        <i class="bi bi-box-arrow-up-right"></i> <?= htmlspecialchars($link_text) ?>
                    </a>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-newspaper" style="font-size: 2.5rem;"></i>
            <h3><?= htmlspecialchars($lang['no_publications']) ?></h3>
        </div>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
