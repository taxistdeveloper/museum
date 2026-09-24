<?php
session_start();
require '../../config.php';

if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['message'] = 'Некорректный запрос.';
    $_SESSION['message_type'] = 'error';
    header('Location: admin/manage_veterans.php');
    exit;
}

// Удаляем запись из базы данных
$query = "DELETE FROM veterans WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['message'] = 'Ветеран успешно удален.';
    $_SESSION['message_type'] = 'success';
} else {
    $_SESSION['message'] = 'Ошибка при удалении ветерана.';
    $_SESSION['message_type'] = 'error';
}

header('Location: admin/manage_veterans.php');
exit;
