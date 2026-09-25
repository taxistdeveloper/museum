<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$query_teachers = "
    SELECT t.*,
           COALESCE(tl.name, t.name) AS localized_name,
           tl.description
    FROM teachers t
    LEFT JOIN teachers_lang tl ON t.id = tl.teacher_id AND tl.language = '" . mysqli_real_escape_string($conn, $db_lang) . "'
    ORDER BY LOWER(COALESCE(tl.name, t.name)) ASC";
$result_teachers = mysqli_query($conn, $query_teachers);
$museum_page_title = $lang['teachers_list'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['teachers']) ?></h1>

<?php while ($teacher = mysqli_fetch_assoc($result_teachers)): ?>
    <article class="profile-card">
        <div class="profile-card__row">
            <img src="../admin/teachers/uploads/<?= htmlspecialchars($teacher['image']) ?>"
                 alt="<?= htmlspecialchars($teacher['localized_name']) ?>"
                 loading="lazy">
            <div>
                <h2><?= htmlspecialchars($teacher['localized_name']) ?></h2>
                <p><strong><?= htmlspecialchars($lang['education']) ?>:</strong></p>
                <?php
                $education = json_decode($teacher['education']);
                if (is_array($education) && count($education) > 0) {
                    echo '<ul>';
                    foreach ($education as $edu) {
                        echo '<li>' . htmlspecialchars($edu) . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p>' . htmlspecialchars($lang['no_education']) . '</p>';
                }
                ?>
                <p><strong><?= htmlspecialchars($lang['yearwork']) ?>:</strong> <?= htmlspecialchars($lang['from_year']) ?> <?= htmlspecialchars($teacher['works_in_college']) ?></p>
                <p><strong><?= htmlspecialchars($lang['work']) ?>:</strong> <?= htmlspecialchars($teacher['position']) ?></p>
                <button class="museum-btn" type="button" data-teacher-id="<?= (int)$teacher['id'] ?>" id="openModal<?= (int)$teacher['id'] ?>">
                    <?= htmlspecialchars($lang['lookachteacher']) ?>
                </button>
            </div>
        </div>
    </article>

    <div class="museum-modal" id="achievementsModal<?= (int)$teacher['id'] ?>" onclick="if(event.target===this)this.classList.remove('active')">
        <div class="museum-modal__card">
            <div class="museum-modal__head">
                <h4><?= htmlspecialchars($lang['achievements']) ?></h4>
                <button class="museum-modal__close" type="button" data-teacher-id="<?= (int)$teacher['id'] ?>" id="closeModal<?= (int)$teacher['id'] ?>" aria-label="<?= htmlspecialchars($lang['close']) ?>">×</button>
            </div>
            <ul class="achievement-list" style="list-style: none; padding: 0; margin: 0;">
                <?php foreach (explode("\n", (string)$teacher['description']) as $line): ?>
                    <?php if (trim($line) !== ''): ?>
                        <li class="achievement-item">
                            <i class="bi bi-award"></i>
                            <div><?= htmlspecialchars($line) ?></div>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <div style="text-align: right; margin-top: 1rem;">
                <button class="museum-btn" type="button" id="closeModalFooter<?= (int)$teacher['id'] ?>"><?= htmlspecialchars($lang['close']) ?></button>
            </div>
        </div>
    </div>
<?php endwhile; ?>

<script>
    document.querySelectorAll('[id^="openModal"]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('achievementsModal' + button.dataset.teacherId).classList.add('active');
        });
    });
    document.querySelectorAll('[id^="closeModal"]').forEach(function (button) {
        button.addEventListener('click', function () {
            var id = button.dataset.teacherId || button.id.replace('closeModalFooter', '');
            document.getElementById('achievementsModal' + id).classList.remove('active');
        });
    });
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
