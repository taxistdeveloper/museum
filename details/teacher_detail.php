<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$teacher_id = (int)($_GET['id'] ?? 0);
$result_teacher = mysqli_query($conn, "SELECT * FROM teachers WHERE id = $teacher_id");
$teacher = mysqli_fetch_assoc($result_teacher);

if (!$teacher) {
    echo "<h2 style='text-align:center;margin-top:3rem;'>Преподаватель не найден</h2>";
    exit;
}

$museum_page_title = $teacher['name'];
$museum_back_href = '../pages/teachers.php?lang=' . urlencode($language);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<article class="profile-card">
    <h1><?= htmlspecialchars($teacher['name']) ?></h1>
    <h3><?= htmlspecialchars($lang['description']) ?></h3>
    <p><?= nl2br(htmlspecialchars((string)$teacher['description'])) ?></p>
</article>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
