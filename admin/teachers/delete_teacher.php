<?php
session_start();
require '../../config.php';

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Получаем ID преподавателя из параметров URL
$teacher_id = $_GET['id'];

// Запрос на удаление преподавателя
$query_delete_teacher = "DELETE FROM teachers WHERE id = '$teacher_id'";
if (mysqli_query($conn, $query_delete_teacher)) {
    echo "<h2 class='has-text-centered'>Преподаватель удален успешно!</h2>";
    echo "<a href='../manage_teachers.php' class='button is-link'>Перейти к списку преподавателей</a>";
} else {
    echo "<h2 class='has-text-centered'>Ошибка удаления преподавателя</h2>";
}
