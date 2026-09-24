<?php
include '../config.php';

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ru';

$query_teachers = "
    SELECT t.*, 
           COALESCE(tl.name, t.name) AS localized_name, 
           tl.description 
    FROM teachers t
    LEFT JOIN teachers_lang tl ON t.id = tl.teacher_id AND tl.language = '$lang'
    ORDER BY LOWER(COALESCE(tl.name, t.name)) ASC";
$result_teachers = mysqli_query($conn, $query_teachers);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Преподаватели</title>
    <link href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-color: #f4f4f7;
            --card-bg: #fff;
            --text-color: #1f1f1f;
            --primary: #0d6efd;
            --shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            --radius: 12px;
        }

        body.dark {
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #e0e0e0;
            --primary: #91caff;
            --shadow: 0 6px 18px rgba(0, 0, 0, 0.3);
        }

        body {
            margin: 0;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: "Montserrat", sans-serif;
            transition: all 0.3s ease;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 1rem;
        }

        .header__container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            padding: 1rem 0;
        }

        .logo img {
            max-height: 80px;
        }

        .language-switcher {
            display: flex;
            gap: 10px;
        }

        .language-switcher a {
            padding: 8px 16px;
            border-radius: 999px;
            border: 1px solid #ccc;
            text-decoration: none;
            background-color: var(--card-bg);
            color: var(--text-color);
            transition: 0.3s;
        }

        .language-switcher a:hover,
        .language-switcher a.active {
            background-color: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .back {
            text-decoration: none;
            font-weight: 500;
            background-color: var(--primary);
            color: white;
            padding: 8px 20px;
            border-radius: var(--radius);
            transition: 0.3s;
        }

        .back:hover {
            background-color: #0056c7;
        }

        .profile-card {
            background: var(--card-bg);
            color: var(--text-color);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .profile-card img {
            max-width: 150px;
            border-radius: 8px;
        }

        .title__title {
            font-weight: 600;
            color: var(--text-color);
        }

        .modal-card-head {
            background: var(--primary);
            color: #fff;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }


        .modal-card-title {
            color: #ffff;

        }

        .modal-close-btn {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .achievement-item {
            background: #f9fafb;
            border: 1px solid #e0e0e0;
            border-left: 5px solid var(--primary);
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            box-shadow: var(--shadow);
        }

        .achievement-text {
            font-size: 1rem;
            color: var(--text-color);
            white-space: pre-line;
        }

        .theme-toggle {
            cursor: pointer;
            font-size: 1.4rem;
            margin-left: 1rem;
        }

        @media (max-width: 768px) {
            .language-switcher {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>

    <script>
        function toggleTheme() {
            document.body.classList.toggle("dark");
        }
    </script>
</head>

<body>
    <div class="container">
        <section class="header">
            <div class="header__container">
                <a class="logo" href="">
                    <img src="https://ff2.object.pscloud.io/giwycqkw/oeqsaqye/eoamgqcg/4384bf6eeeace361ca86ed2a4e97d2975c665532.png" alt="Logo" />
                </a>
                <div class="right">
                    <a class="back" href="../index.php">Назад</a>
                    <span class="theme-toggle" onclick="toggleTheme()">🌓</span>
                </div>
            </div>
        </section>

        <div class="language-switcher has-text-centered mb-4">
            <a href="?lang=ru" class="<?= $lang == 'ru' ? 'active' : '' ?>">🇷🇺 Русский</a>
            <a href="?lang=kz" class="<?= $lang == 'kz' ? 'active' : '' ?>">🇰🇿 Қазақша</a>
            <a href="?lang=en" class="<?= $lang == 'en' ? 'active' : '' ?>">🇬🇧 English</a>
        </div>

        <section class="section">
            <div class="container">
                <?php while ($teacher = mysqli_fetch_assoc($result_teachers)): ?>
                    <div class="profile-card">
                        <div class="columns is-vcentered">
                            <div class="column is-narrow">
                                <img src="../admin/teachers/uploads/<?= $teacher['image'] ?>" alt="">
                            </div>
                            <div class="column">
                                <h2 class="title is-4"><?= htmlspecialchars($teacher['localized_name']) ?></h2>
                                <p><strong class="title__title"><?= $lang == 'ru' ? 'ОБРАЗОВАНИЕ' : ($lang == 'kz' ? 'БІЛІМІ' : 'EDUCATION') ?>:</strong></p>
                                <?php
                                $education = json_decode($teacher['education']);
                                if (is_array($education) && count($education) > 0) {
                                    echo '<ul>';
                                    foreach ($education as $edu) {
                                        echo '<li>' . htmlspecialchars($edu) . '</li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<p>' . ($lang == 'ru' ? 'Образование не указано.' : ($lang == 'kz' ? 'Білімі көрсетілмеген.' : 'No education specified.')) . '</p>';
                                }
                                ?>
                                <p><strong class="title__title"><?= $lang == 'ru' ? 'РАБОТАЕТ В КОЛЛЕДЖЕ' : ($lang == 'kz' ? 'КОЛЛЕДЖДЕ ЖҰМЫС ІСТЕЙДІ' : 'WORKS IN COLLEGE') ?>:</strong> с <?= $teacher['works_in_college'] ?> года</p>
                                <p><strong class="title__title"><?= $lang == 'ru' ? 'ДОЛЖНОСТЬ' : ($lang == 'kz' ? 'ЛАУАЗЫМЫ' : 'POSITION') ?>:</strong> <?= $teacher['position'] ?></p>
                                <button class="button is-link is-rounded is-info is-normal mt-3" data-teacher-id="<?= $teacher['id'] ?>" id="openModal<?= $teacher['id'] ?>">
                                    <?= $lang == 'ru' ? 'Посмотреть награды и достижения' : ($lang == 'kz' ? 'Марапаттар мен жетістіктерді қарау' : 'View awards and achievements') ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="modal" id="achievementsModal<?= $teacher['id'] ?>">
                        <div class="modal-background"></div>
                        <div class="modal-card">
                            <header class="modal-card-head">
                                <p class="modal-card-title">
                                    <?= $lang == 'ru' ? 'Награды, Звания, Достижения' : ($lang == 'kz' ? 'Марапаттар, атақтар, жетістіктер' : 'Awards, Titles, Achievements') ?>
                                </p>
                                <button class="modal-close-btn" data-teacher-id="<?= $teacher['id'] ?>" id="closeModal<?= $teacher['id'] ?>">&times;</button>
                            </header>
                            <section class="modal-card-body">
                                <ul class="achievement-list">
                                    <?php foreach (explode("\n", $teacher['description']) as $line): ?>
                                        <?php if (trim($line) !== ''): ?>
                                            <li class="achievement-item">
                                                <i class="bi bi-award achievement-icon"></i>
                                                <div class="achievement-text"><?= htmlspecialchars($line) ?></div>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </section>
                            <footer class="modal-card-foot">
                                <button class="button is-success" id="closeModalFooter<?= $teacher['id'] ?>">
                                    <?= $lang == 'ru' ? 'Закрыть' : ($lang == 'kz' ? 'Жабу' : 'Close') ?>
                                </button>
                            </footer>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </div>

    <script>
        const modalButtons = document.querySelectorAll('[id^="openModal"]');
        const closeModalButtons = document.querySelectorAll('[id^="closeModal"]');
        const closeModalFooterButtons = document.querySelectorAll('[id^="closeModalFooter"]');

        modalButtons.forEach(button => {
            button.addEventListener('click', () => {
                const teacherId = button.dataset.teacherId;
                document.getElementById(`achievementsModal${teacherId}`).classList.add('is-active');
            });
        });

        [...closeModalButtons, ...closeModalFooterButtons].forEach(button => {
            button.addEventListener('click', () => {
                const teacherId = button.dataset.teacherId || button.id.replace('closeModalFooter', '');
                document.getElementById(`achievementsModal${teacherId}`).classList.remove('is-active');
            });
        });
    </script>
</body>

</html>