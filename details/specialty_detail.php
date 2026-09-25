<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$specialty_id = (int)($_GET['id'] ?? 0);
$result_specialty = mysqli_query($conn, "SELECT * FROM specialties WHERE id = $specialty_id");
$specialty = mysqli_fetch_assoc($result_specialty);

if (!$specialty) {
    echo "<h2 style='text-align:center;margin-top:3rem;'>Специальность не найдена</h2>";
    exit;
}

$museum_page_title = $specialty['name'];
$museum_back_href = '../pages/speciality.php?lang=' . urlencode($language);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<article class="prose-card">
    <h1><?= htmlspecialchars($specialty['name']) ?></h1>
    <p><strong><?= htmlspecialchars($lang['description']) ?>:</strong></p>
    <p><?= nl2br(htmlspecialchars((string)$specialty['description'])) ?></p>
</article>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
