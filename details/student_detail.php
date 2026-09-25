<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$id = (int)($_GET['id'] ?? 0);
$result = mysqli_query($conn, "SELECT * FROM students WHERE id = $id");
$student = mysqli_fetch_assoc($result);

if (!$student) {
    echo "<h2 style='text-align:center;margin-top:3rem;'>Студент не найден</h2>";
    exit;
}

$museum_page_title = $student['name'];
$museum_back_href = '../pages/student.php?lang=' . urlencode($language);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<article class="profile-card">
    <div class="profile-card__row">
        <?php if (!empty($student['photo']) || !empty($student['image'])): ?>
            <img src="../admin/students/uploads/<?= htmlspecialchars($student['image'] ?? $student['photo']) ?>"
                 alt="<?= htmlspecialchars($student['name']) ?>">
        <?php endif; ?>
        <div>
            <h1><?= htmlspecialchars($student['name']) ?></h1>
            <p><strong><?= htmlspecialchars($lang['biography']) ?>:</strong></p>
            <p><?= nl2br(htmlspecialchars((string)($student['biography'] ?? $student['achievements'] ?? ''))) ?></p>
        </div>
    </div>
</article>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
