<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../config.php';

// Получаем ID публикации
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: manage_media.php');
    exit();
}

// Удаляем публикацию
$query = "DELETE FROM media_publications WHERE id = $id";

if (mysqli_query($conn, $query)) {
    header('Location: manage_media.php?deleted=1');
} else {
    header('Location: manage_media.php?error=1');
}

exit();
?>
