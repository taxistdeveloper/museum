<?php
include '../config.php';

// Запрос к базе данных
$query_gallery = "SELECT * FROM gallery ORDER BY created_at DESC";
$result_gallery = mysqli_query($conn, $query_gallery);

if (!$result_gallery) {
    die("Ошибка в запросе: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Фото архив</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Montserrat' sans-serif;
        }

        .gallery-container {
            margin-top: 30px;
        }

        .gallery-item {
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .gallery-item:hover {
            transform: scale(1.05);
        }

        .card {
            border-radius: 12px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .description {
            max-height: 50px;
            overflow: hidden;
            position: relative;
            transition: max-height 0.3s ease;
        }

        .description.expanded {
            max-height: none;
        }

        .show-more {
            cursor: pointer;
            color: #3273dc;
            font-size: 0.9rem;
            display: none;
        }
    </style>
</head>

<body>
    <div class="has-text-centered">
        <a href="./gallery_video.php" class="button is-link is-rounded mt-4">Назад</a>
    </div>
    <div class="container gallery-container">
        <h1 class="title has-text-centered">Фото архив</h1>

        <!-- Elfsight Instagram Feed | Untitled Instagram Feed -->
        <script src="https://static.elfsight.com/platform/platform.js" async></script>
        <div class="elfsight-app-e0cb7e81-aedb-45fe-9615-c777dbe6e3db" data-elfsight-app-lazy></div>
        <?php if (mysqli_num_rows($result_gallery) === 0) { ?>
            <p class="has-text-centered">Нет изображений для отображения.</p>
        <?php } ?>

        <!-- <div class="columns is-multiline is-justify-content-center">
            <?php while ($row = mysqli_fetch_assoc($result_gallery)) { ?>
                <div class="column is-one-quarter gallery-item">
                    <div class="card">
                        <div class="card-image" onclick="openModal('<?= htmlspecialchars($row['file_name']) ?>', '<?= htmlspecialchars($row['description']) ?>')">
                            <img src="../admin/gallery/uploads/<?= htmlspecialchars($row['file_name']) ?>" alt="Фото">
                        </div>
                        <div class="card-content">
                            <p class="description"><?= htmlspecialchars($row['description']) ?></p>
                            <span class="show-more" onclick="toggleDescription(this)">Показать больше</span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div> -->


    </div>

    <!-- Модальное окно -->
    <!-- <div id="modal" class="modal">
        <div class="modal-background" onclick="closeModal()"></div>
        <div class="modal-content">
            <div class="box">
                <img id="modal-image" src="" alt="Фото">
                <p id="modal-desc" class="has-text-centered mt-3"></p>
            </div>
        </div>
        <button class="modal-close is-large" onclick="closeModal()"></button>
    </div>

    <script>
        function openModal(image, desc) {
            document.getElementById('modal-image').src = '../admin/gallery/uploads/' + image;
            document.getElementById('modal-desc').textContent = desc;
            document.getElementById('modal').classList.add('is-active');
        }

        function closeModal() {
            document.getElementById('modal').classList.remove('is-active');
        }

        function toggleDescription(button) {
            let desc = button.previousElementSibling;
            desc.classList.toggle('expanded');
            button.textContent = desc.classList.contains('expanded') ? 'Скрыть' : 'Показать больше';
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.description').forEach(desc => {
                if (desc.scrollHeight > 50) {
                    desc.nextElementSibling.style.display = 'inline';
                }
            });
        }); -->
    </script>

</body>

</html>