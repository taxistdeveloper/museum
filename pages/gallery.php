<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$result_gallery = mysqli_query($conn, "SELECT * FROM gallery ORDER BY created_at DESC");
if (!$result_gallery) {
    die("Ошибка в запросе: " . mysqli_error($conn));
}
$museum_page_title = $lang['photo'];
$museum_back_href = 'gallery_video.php?lang=' . urlencode($language);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['photo']) ?></h1>

<?php if (mysqli_num_rows($result_gallery) === 0): ?>
    <p class="empty-state"><?= htmlspecialchars($lang['no_images']) ?></p>
<?php else: ?>
    <div class="gallery-grid">
        <?php while ($row = mysqli_fetch_assoc($result_gallery)): ?>
            <?php
            $caption = preg_replace('/^[\s\x{00A0}\x{2800}]+|[\s\x{00A0}\x{2800}]+$/u', '', (string)$row['description']);
            ?>
            <button type="button" class="gallery-tile" onclick="openModal('<?= htmlspecialchars($row['file_name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($caption, ENT_QUOTES) ?>')">
                <img src="../admin/gallery/uploads/<?= htmlspecialchars($row['file_name']) ?>"
                     alt="<?= htmlspecialchars($caption !== '' ? $caption : $lang['photo']) ?>"
                     loading="lazy">
                <?php if ($caption !== ''): ?>
                    <p><?= htmlspecialchars($caption) ?></p>
                <?php endif; ?>
            </button>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<div class="museum-modal" id="modal" onclick="if(event.target===this)closeModal()">
    <div class="museum-modal__card">
        <div class="museum-modal__head">
            <span></span>
            <button class="museum-modal__close" type="button" onclick="closeModal()" aria-label="<?= htmlspecialchars($lang['close']) ?>">×</button>
        </div>
        <img id="modal-image" src="" alt="<?= htmlspecialchars($lang['photo']) ?>">
        <p id="modal-desc" style="text-align:center;margin-top:1rem;"></p>
    </div>
</div>

<script>
    function openModal(image, desc) {
        document.getElementById('modal-image').src = '../admin/gallery/uploads/' + image;
        document.getElementById('modal-desc').textContent = desc;
        document.getElementById('modal').classList.add('active');
    }
    function closeModal() {
        document.getElementById('modal').classList.remove('active');
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
