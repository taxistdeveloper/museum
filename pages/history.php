<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

$is_logged_in = isset($_SESSION['user_id']);
$result = mysqli_query($conn, "SELECT * FROM history ORDER BY created_at ASC, id ASC");

if (isset($_GET['delete_id']) && $is_logged_in) {
    $delete_id = (int)$_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM history WHERE id = $delete_id");
    header("Location: ./history.php?lang=" . urlencode($language) . "&message=deleted");
    exit;
}

$message = '';
if (isset($_GET['message'])) {
    $msgMap = [
        'success' => $lang['message_success'],
        'deleted' => $lang['message_deleted'],
    ];
    $message = $msgMap[$_GET['message']] ?? '';
}

$museum_page_title = $lang['history_title'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['historytitle']) ?></h1>

<?php if ($message): ?>
    <div id="success-message" class="notification"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="timeline">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <article class="timeLine-item">
            <div class="timeLine-item__card">
                <header class="timeLine-item__head">
                    <span class="timeLine-item__year"><?= htmlspecialchars($row['title']) ?></span>
                </header>
                <p class="timeLine-item__text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                <?php if ($is_logged_in): ?>
                    <div class="timeline-footer">
                        <a href="../admin/history/edit_history.php?id=<?= (int)$row['id'] ?>" class="museum-btn museum-btn--info"><?= htmlspecialchars($lang['edit']) ?></a>
                        <a href="?delete_id=<?= (int)$row['id'] ?>&lang=<?= urlencode($language) ?>" class="museum-btn museum-btn--danger" onclick="return confirm('<?= htmlspecialchars($lang['delete_confirm']) ?>')"><?= htmlspecialchars($lang['delete']) ?></a>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php if ($is_logged_in): ?>
    <div class="timeline-add">
        <a href="../admin/history/add_history.php" class="museum-btn"><?= htmlspecialchars($lang['add_history']) ?></a>
    </div>
<?php endif; ?>

<script>
    const msg = document.getElementById('success-message');
    if (msg) {
        setTimeout(function () {
            msg.style.opacity = '0';
            setTimeout(function () { msg.remove(); }, 600);
        }, 2000);
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
