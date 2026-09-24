<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include '../config.php';

// Получаем ID пользователя из URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    header('Location: manage_users.php');
    exit();
}

// Нельзя удалить самого себя
if ($user_id == $_SESSION['user_id']) {
    header('Location: manage_users.php?error=self_delete');
    exit();
}

// Удаляем пользователя
$query = "DELETE FROM users WHERE id = $user_id";
if (mysqli_query($conn, $query)) {
    header('Location: manage_users.php?success=deleted');
} else {
    header('Location: manage_users.php?error=delete_failed');
}
exit();
