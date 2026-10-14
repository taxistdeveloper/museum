<?php
if (!function_exists('admin_preview')) {
    function admin_preview(?string $text, int $limit = 110): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)));
        if ($text === '') {
            return '';
        }
        if (mb_strlen($text) <= $limit) {
            return htmlspecialchars($text);
        }
        return htmlspecialchars(mb_substr($text, 0, $limit - 1)) . '…';
    }
}
$admin_root = realpath(__DIR__ . '/..');
$script_dir = realpath(dirname($_SERVER['SCRIPT_FILENAME'] ?? ''));
$depth = 0;
if ($admin_root && $script_dir && str_starts_with($script_dir, $admin_root)) {
    $suffix = trim(substr($script_dir, strlen($admin_root)), DIRECTORY_SEPARATOR);
    if ($suffix !== '') {
        $depth = substr_count($suffix, DIRECTORY_SEPARATOR) + 1;
    }
}
$up_admin = $depth === 0 ? '' : str_repeat('../', $depth);
$up_site = str_repeat('../', $depth + 1);
$admin_script = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
$admin_auth_page = in_array($admin_script, ['login.php', 'register.php'], true);
$admin_user = $_SESSION['username'] ?? '';
?>
<header class="admin-top">
    <a class="admin-brand" href="<?= $admin_auth_page ? $up_site . 'index.php' : $up_admin . 'index.php' ?>">
        <img src="<?= $up_site ?>assets/img/logo.png" alt="КТСК">
        <span>Админ-панель</span>
    </a>
    <?php if (!$admin_auth_page): ?>
        <nav class="admin-nav">
            <?php if ($admin_script !== 'index.php'): ?>
                <a href="<?= $up_admin ?>index.php">Разделы</a>
            <?php endif; ?>
            <a href="<?= $up_site ?>index.php">Сайт</a>
            <?php if ($admin_user !== ''): ?>
                <span class="admin-user"><?= htmlspecialchars($admin_user) ?></span>
            <?php endif; ?>
            <a href="<?= $up_admin ?>logout.php">Выйти</a>
        </nav>
    <?php else: ?>
        <nav class="admin-nav">
            <a href="<?= $up_site ?>index.php">На сайт</a>
        </nav>
    <?php endif; ?>
</header>
