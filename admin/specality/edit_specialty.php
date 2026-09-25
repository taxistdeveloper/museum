<?php
session_start();
require '../../config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$lang = $_GET['lang'] ?? 'ru';

// Получаем специальность
$query = "SELECT * FROM specialties WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$specialty = mysqli_fetch_assoc($result);

if (!$specialty) {
    $_SESSION['message'] = 'Специальность не найдена!';
    $_SESSION['message_type'] = 'warning';
    header('Location: manage_specialties.php');
    exit();
}

// Получаем перевод
$query_lang = "SELECT * FROM specialties_lang WHERE specialty_id = $id AND language = '$lang'";
$result_lang = mysqli_query($conn, $query_lang);
$specialty_lang = mysqli_fetch_assoc($result_lang);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $icon = mysqli_real_escape_string($conn, $_POST['icon']);

    // Обновляем иконку
    mysqli_query($conn, "UPDATE specialties SET icon = '$icon' WHERE id = $id");

    // Обновляем или создаём перевод
    $check = mysqli_query($conn, "SELECT id FROM specialties_lang WHERE specialty_id = $id AND language = '$lang'");
    if (mysqli_fetch_assoc($check)) {
        mysqli_query($conn, "UPDATE specialties_lang SET name = '$name', description = '$description' WHERE specialty_id = $id AND language = '$lang'");
    } else {
        mysqli_query($conn, "INSERT INTO specialties_lang (specialty_id, language, name, description) VALUES ($id, '$lang', '$name', '$description')");
    }

    $_SESSION['message'] = 'Специальность обновлена!';
    $_SESSION['message_type'] = 'success';
    header("Location: ?id=$id&lang=$lang");
    exit();
}

// Подгружаем иконки из icon_library
$icon_options = [];
$icon_result = mysqli_query($conn, "SELECT icon_class FROM icon_library ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($icon_result)) {
    $icon_options[] = $row['icon_class'];
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактировать специальность</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }

        .container-box {
            max-width: 860px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #007bff;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            padding: 30px;
        }

        .btn-back {
            background: #6c757d;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
        }

        .tabs a {
            padding: 10px 15px;
            margin-right: 5px;
            background: #e9ecef;
            border-radius: 5px;
            color: #007bff;
            text-decoration: none;
        }

        .tabs a.active {
            background: #007bff;
            color: white;
        }

        .icon-box {
            width: 100px;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .icon-box:hover {
            background: #f0f0f0;
        }

        .icon-box.selected {
            border: 2px solid #007bff;
            background: #e7f1ff;
        }

        .icon-box i {
            font-size: 1.8rem;
            color: #007bff;
        }

        .icon-box input {
            display: none;
        }
    </style>
</head>

<body>

    <div class="container-box">
        <div class="header">
            <h1>Редактировать специальность</h1>
        </div>

        <div class="content">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                    <?= $_SESSION['message'] ?>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <ul class="nav nav-tabs justify-content-center mb-3">
                <li class="nav-item"><a class="nav-link <?= $lang == 'ru' ? 'active' : '' ?>" href="?id=<?= $id ?>&lang=ru">Русский</a></li>
                <li class="nav-item"><a class="nav-link <?= $lang == 'kz' ? 'active' : '' ?>" href="?id=<?= $id ?>&lang=kz">Қазақша</a></li>
                <li class="nav-item"><a class="nav-link <?= $lang == 'en' ? 'active' : '' ?>" href="?id=<?= $id ?>&lang=en">English</a></li>
            </ul>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Название</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($specialty_lang['name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($specialty_lang['description'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Выберите иконку</label>
                    <div class="d-flex flex-wrap gap-3" id="iconGallery">
                        <?php foreach ($icon_options as $icon): ?>
                            <label class="icon-box <?= $specialty['icon'] == $icon ? 'selected' : '' ?>">
                                <input type="radio" name="icon" value="<?= $icon ?>" <?= $specialty['icon'] == $icon ? 'checked' : '' ?>>
                                <i class="bi <?= $icon ?>"></i>
                                <div style="font-size: 0.75rem; margin-top: 5px;"><?= $icon ?></div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Кнопка для модального выбора -->
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#iconModal">
                            Выбрать из всех иконок
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="manage_specialties.php" class="btn-back">Назад</a>
            </form>
        </div>
    </div>

    <!-- Модалка -->
    <div class="modal fade" id="iconModal" tabindex="-1" aria-labelledby="iconModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Выбор иконки</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="searchIcon" class="form-control mb-3" placeholder="Поиск иконки...">
                    <div class="d-flex flex-wrap gap-3" id="modalIconList">
                        <?php foreach ($icon_options as $icon): ?>
                            <div class="icon-box" data-icon="<?= $icon ?>">
                                <i class="bi <?= $icon ?>"></i>
                                <div class="small mt-1"><?= $icon ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script>
        const iconBoxes = document.querySelectorAll('#iconGallery .icon-box');
        iconBoxes.forEach(box => {
            box.addEventListener('click', () => {
                iconBoxes.forEach(b => b.classList.remove('selected'));
                box.classList.add('selected');
                box.querySelector('input').checked = true;
            });
        });

        // Поиск по модалке
        const searchInput = document.getElementById('searchIcon');
        const modalIcons = document.querySelectorAll('#modalIconList .icon-box');
        searchInput.addEventListener('input', function() {
            const val = this.value.toLowerCase();
            modalIcons.forEach(icon => {
                const name = icon.dataset.icon.toLowerCase();
                icon.style.display = name.includes(val) ? 'block' : 'none';
            });
        });

        // Клик по иконке в модалке
        modalIcons.forEach(icon => {
            icon.addEventListener('click', () => {
                const iconClass = icon.dataset.icon;
                const radio = document.querySelector(`#iconGallery input[value="${iconClass}"]`);
                if (radio) {
                    radio.checked = true;
                    iconBoxes.forEach(b => b.classList.remove('selected'));
                    radio.closest('.icon-box').classList.add('selected');
                }
                const modal = bootstrap.Modal.getInstance(document.getElementById('iconModal'));
                modal.hide();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>