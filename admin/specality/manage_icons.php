<?php
session_start();
require '../../config.php';

// Обработка добавления иконки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['icon_class'])) {
        $icon = mysqli_real_escape_string($conn, $_POST['icon_class']);
        mysqli_query($conn, "INSERT INTO icon_library (icon_class, is_image) VALUES ('$icon', 0)");
        $_SESSION['message'] = 'Иконка добавлена!';
    }

    if (!empty($_FILES['icon_image']['name'])) {
        $target_dir = "../../uploads/icons/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $filename = time() . '_' . basename($_FILES["icon_image"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["icon_image"]["tmp_name"], $target_file)) {
            $rel_path = 'uploads/icons/' . $filename;
            mysqli_query($conn, "INSERT INTO icon_library (icon_class, is_image, file_path) VALUES ('', 1, '$rel_path')");
            $_SESSION['message'] = 'Картинка-иконка загружена!';
        } else {
            $_SESSION['message'] = 'Ошибка загрузки изображения!';
        }
    }

    header("Location: manage_icons.php");
    exit();
}

// Удаление иконки
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = mysqli_query($conn, "SELECT * FROM icon_library WHERE id = $id");
    $icon = mysqli_fetch_assoc($res);

    if ($icon['is_image'] && file_exists('../../' . $icon['file_path'])) {
        unlink('../../' . $icon['file_path']);
    }

    mysqli_query($conn, "DELETE FROM icon_library WHERE id = $id");
    $_SESSION['message'] = 'Иконка удалена!';
    header("Location: manage_icons.php");
    exit();
}

// Получение всех иконок
$result = mysqli_query($conn, "SELECT * FROM icon_library ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Управление иконками</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .icon-preview {
            font-size: 1.8rem;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-preview img {
            max-height: 48px;
        }
    </style>
</head>

<body class="bg-light py-4">

    <div class="container">
        <h2 class="mb-4">Управление иконками</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="mb-4 row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Bootstrap Icon (например: bi-camera)</label>
                <input type="text" name="icon_class" class="form-control" placeholder="bi-...">
            </div>
            <div class="col-md-5">
                <label class="form-label">Загрузить иконку (PNG, SVG, JPG)</label>
                <input type="file" name="icon_image" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Добавить</button>
            </div>
        </form>

        <div class="row g-3">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-3">
                    <div class="border p-3 text-center bg-white rounded shadow-sm">
                        <div class="icon-preview">
                            <?php if ($row['is_image']): ?>
                                <img src="../../<?= htmlspecialchars($row['file_path']) ?>" alt="icon">
                            <?php else: ?>
                                <i class="bi <?= htmlspecialchars($row['icon_class']) ?>"></i>
                            <?php endif; ?>
                        </div>
                        <div class="small mt-2 text-break">
                            <?= $row['is_image'] ? htmlspecialchars($row['file_path']) : htmlspecialchars($row['icon_class']) ?>
                        </div>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger mt-2">Удалить</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</body>

</html>