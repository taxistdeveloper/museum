<?php
require '../../config.php';
session_start();  // Стартуем сессию для передачи сообщений

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Удаляем данные о директоре
    $query = "DELETE FROM directors WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        $_SESSION['message'] = 'Директор удалён успешно!';
        $_SESSION['message_type'] = 'success';  // Успешное удаление
    } else {
        $_SESSION['message'] = 'Ошибка удаления директора.';
        $_SESSION['message_type'] = 'error';  // Ошибка
    }

    header('Location: admin/manage_directors.php');
    exit;
} else {
    die("ID не задан.");
}
