<?php
require '../config.php';

// Получаем всех директоров
$query_directors = "SELECT * FROM directors ORDER BY created_at DESC";
$result_directors = mysqli_query($conn, $query_directors);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="../assets/main.css" />
</head>

<body>
    <div class="container">
        <section class="header">
            <div class="header__container">
                <a class="logo" href="">
                    <img
                        src="https://ktsk.edu.kz/wp-content/uploads/2024/10/logo.png"
                        alt="" />
                </a>
                <div class="right">
                    <div class="search">
                        <a class="back" href="/">Назад</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="HistoryPeople">
            <div class="history__container">
                <div class="historyPeople__title">Директора учебного заведения</div>
                <div class="historyPeople__content">
                    За годы существования учебного заведения менялось ее наименование и
                    организационно-правовая форма в зависимости от политических,
                    социально-экономических, образовательных реформ в стране.
                    Соответственно и назначение первых руководителей зависело от
                    профессиональных задач, которые стояли на разных этапах
                    образовательной деятельности учебного заведения.
                </div>
                <div class="humansGrid">
                    <?php while ($director = mysqli_fetch_assoc($result_directors)): ?>
                        <div class="HumanCard">
                            <div class="image">
                                <!-- Фото директора -->
                                <img src="../admin/directors/assets/images/<?= $director['photo'] ?>" alt="Фото директора" class="director-image">
                            </div>
                            <div class="right">
                                <div class="historyPeople__name">
                                    <?= $director['name'] ?>
                                </div>
                                <div class="historyPeople__button">
                                    <a href="../details/historypeople__directors__view.php?id=<?= $director['id'] ?>" class="button is-primary">Подробнее</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>

                </div>




            </div>
        </section>
    </div>
</body>

</html>