<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Завершаем выполнение скрипта
}

require '../../config.php';

$id = intval($_GET['id']);

// Получаем данные изображения
$query = "SELECT file_name FROM gallery WHERE id = $id";
$result = mysqli_query($conn, $query);
$image = mysqli_fetch_assoc($result);

if ($image) {
    $filePath = 'uploads/' . $image['file_name'];
    // Удаляем файл
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Удаляем запись из базы
    $delete = "DELETE FROM gallery WHERE id = $id";
    if (mysqli_query($conn, $delete)) {
        header("Location: admin/manage_gallery.php?success=1");
        exit;
    } else {
        die("Ошибка при удалении записи.");
    }
} else {
    die("Изображение не найдено.");
}
