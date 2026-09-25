<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$result_students = mysqli_query($conn, "SELECT * FROM students ORDER BY LOWER(name) ASC");
$museum_page_title = $lang['Ourstudent'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['Ourstudent']) ?></h1>
<div class="student-grid">
    <?php while ($student = mysqli_fetch_assoc($result_students)): ?>
        <div class="student-card">
            <div class="student-card__row">
                <img class="student-img"
                     src="../admin/students/uploads/<?= htmlspecialchars($student['image']) ?>"
                     alt="<?= htmlspecialchars($student['name']) ?>"
                     loading="lazy">
                <div>
                    <h3><?= htmlspecialchars($student['name']) ?></h3>
                    <p><strong><?= htmlspecialchars($lang['group']) ?>:</strong> <?= htmlspecialchars($student['group_name'] ?? '—') ?></p>
                    <button class="museum-btn" type="button" onclick="openModal(<?= (int)$student['id'] ?>)">
                        <?= htmlspecialchars($lang['lookachstudent']) ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="museum-modal" id="modal<?= (int)$student['id'] ?>" onclick="if(event.target===this)closeModal(<?= (int)$student['id'] ?>)">
            <div class="museum-modal__card">
                <div class="museum-modal__head">
                    <h4><?= htmlspecialchars($lang['achstudent']) ?>: <?= htmlspecialchars($student['name']) ?></h4>
                    <button class="museum-modal__close" type="button" onclick="closeModal(<?= (int)$student['id'] ?>)" aria-label="<?= htmlspecialchars($lang['close']) ?>">×</button>
                </div>
                <div>
                    <?php
                    $achievements_array = json_decode(trim((string)$student['achievements']), true);
                    if (is_array($achievements_array) && count($achievements_array) > 0):
                        $i = 1;
                        foreach ($achievements_array as $item):
                    ?>
                        <div class="achievement-item">
                            <strong><?= $i++ ?>.</strong>
                            <div><?= nl2br(htmlspecialchars($item)) ?></div>
                        </div>
                    <?php
                        endforeach;
                    else:
                    ?>
                        <p style="color: var(--muted);"><em><?= htmlspecialchars($lang['no_achievements']) ?></em></p>
                    <?php endif; ?>
                </div>
                <div style="text-align: right; margin-top: 1rem;">
                    <button class="museum-btn" type="button" onclick="closeModal(<?= (int)$student['id'] ?>)"><?= htmlspecialchars($lang['close']) ?></button>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script>
    function openModal(id) {
        document.getElementById('modal' + id).classList.add('active');
    }
    function closeModal(id) {
        document.getElementById('modal' + id).classList.remove('active');
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.museum-modal.active').forEach(function (el) {
                el.classList.remove('active');
            });
        }
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
