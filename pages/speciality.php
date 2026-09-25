<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$db = mysqli_real_escape_string($conn, $db_lang);
$query = "SELECT s.*, sl.name AS translated_name, sl.description AS translated_description
          FROM specialties s
          LEFT JOIN specialties_lang sl ON s.id = sl.specialty_id AND sl.language = '$db'
          ORDER BY s.created_at DESC";
$result = mysqli_query($conn, $query);
$museum_page_title = $lang['specialties'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['specialties']) ?></h1>
<div class="museum-grid">
    <?php while ($specialty = mysqli_fetch_assoc($result)): ?>
        <article class="museum-card specialty-card">
            <h5>
                <i class="bi <?= htmlspecialchars($specialty['icon'] ?? 'bi-book') ?>"></i>
                <?= htmlspecialchars($specialty['translated_name'] ?? $specialty['name']) ?>
            </h5>
            <p><?= nl2br(htmlspecialchars($specialty['translated_description'] ?? $specialty['description'])) ?></p>
        </article>
    <?php endwhile; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
