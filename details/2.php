<?php
$id = (int)($_GET['id'] ?? 0);
$lang = $_GET['lang'] ?? 'ru';
header('Location: veteran_detail.php?id=' . $id . '&lang=' . urlencode($lang));
exit;
