<?php
session_start();

// Проверяем, если сессия не установлена, перенаправляем на страницу авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit(); // Завершаем выполнение скрипта
}

require '../../config.php';

$id = intval($_GET['id']);
$query = "SELECT * FROM gallery WHERE id = $id";
$result = mysqli_query($conn, $query);
$image = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = htmlspecialchars($_POST['description']);
    $update = "UPDATE gallery SET description = '$description' WHERE id = $id";

    if (mysqli_query($conn, $update)) {

        header("Location: admin/manage_gallery.php?success=1");
        exit;
    } else {
        $errorMsg = "Ошибка при обновлении данных.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать описание</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/admin.css">
</head>

<body>
    <?php require __DIR__ . '/../includes/topbar.php'; ?>

    <div class="container mt-5">
        <header class="page-head">
            <div>
                <a class="back-link" href="../manage_gallery.php">Фото</a>
                <h1>Подпись к фото</h1>
            </div>
        </header>

        <?php if (isset($errorMsg)) { ?>
            <div class="alert alert-danger"><?= $errorMsg ?></div>
        <?php } ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Новое описание</label>
                <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($image['description']) ?></textarea>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="../manage_gallery.php" class="btn btn-light">Назад</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>