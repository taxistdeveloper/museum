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
<div class="teacher-grid">
<?php while ($teacher = mysqli_fetch_assoc($result_teachers)): ?>
    <article class="teacher-card">
        <img class="teacher-card__img"
             src="../admin/teachers/uploads/<?= htmlspecialchars($teacher['image']) ?>"
             alt="<?= htmlspecialchars($teacher['localized_name']) ?>"
             loading="lazy">
        <h3 class="teacher-card__name"><?= htmlspecialchars($teacher['localized_name']) ?></h3>
        <div class="teacher-card__meta">
            <p>
                <span><?= htmlspecialchars($lang['work']) ?></span>
                <?= htmlspecialchars($teacher['position']) ?>
            </p>
            <p>
                <span><?= htmlspecialchars($lang['yearwork']) ?></span>
                <?= htmlspecialchars($lang['from_year']) ?> <?= htmlspecialchars($teacher['works_in_college']) ?>
            </p>
            <p>
                <span><?= htmlspecialchars($lang['education']) ?></span>
                <?php
                $education = json_decode($teacher['education']);
                if (is_array($education) && count($education) > 0) {
                    echo implode('<br>', array_map(function ($edu) {
                        return htmlspecialchars((string)$edu);
                    }, $education));
                } else {
                    echo htmlspecialchars($lang['no_education']);
                }
                ?>
            </p>
        </div>
        <button class="museum-btn teacher-card__btn" type="button" data-teacher-id="<?= (int)$teacher['id'] ?>" id="openModal<?= (int)$teacher['id'] ?>">
            <?= htmlspecialchars($lang['lookachteacher']) ?>
        </button>
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
</div>

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
