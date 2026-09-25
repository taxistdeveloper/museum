<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../config.php';

// Получаем ID пользователя и статус из URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? (int)$_GET['status'] : 0;

if ($user_id <= 0) {
    header('Location: manage_users.php');
    exit();
}

// Нельзя заблокировать самого себя
if ($user_id == $_SESSION['user_id'] && $status == 0) {
    header('Location: manage_users.php?error=self_block');
    exit();
}

$has_is_active = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'is_active'");
if (!$has_is_active || mysqli_num_rows($has_is_active) === 0) {
    header('Location: manage_users.php?error=status_failed');
    exit();
}

$query = "UPDATE users SET is_active = $status WHERE id = $user_id";
if (mysqli_query($conn, $query)) {
    $action = $status ? 'activated' : 'blocked';
    header("Location: manage_users.php?success=$action");
} else {
    header('Location: manage_users.php?error=status_failed');
}
exit();
