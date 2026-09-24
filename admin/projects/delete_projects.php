<?php
session_start();
require '../../config.php';

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Проверяем соединение с базой данных
if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

// Удаление проекта
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "DELETE FROM projects WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Проект успешно удален.';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Ошибка при удалении проекта: ' . mysqli_error($conn);
        $_SESSION['message_type'] = 'error';
    }

    header('Location: admin/manage_projects.php');
    exit();
} else {
    $_SESSION['message'] = 'Некорректный запрос.';
    $_SESSION['message_type'] = 'warning';
    header('Location: admin/manage_projects.php');
    exit();
}
