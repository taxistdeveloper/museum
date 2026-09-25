<?php
include '../config.php';

// Получаем все публикации СМИ
$query = "SELECT * FROM media_publications ORDER BY publication_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>СМИ о нас</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f4f4f7;
            --card-bg: #fff;
            --text-color: #1f1f1f;
            --primary: #0d6efd;
            --shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            --radius: 12px;
        }

        body.dark {
            --bg-color: #121212;
            --card-bg: #1e1e1e;
            --text-color: #e0e0e0;
            --primary: #91caff;
            --shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: 0.3s ease;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 1.5rem;
        }

        .header__container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .logo img {
            max-height: 70px;
        }

        .back {
            text-decoration: none;
            font-weight: 500;
            background-color: var(--primary);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: var(--radius);
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back:hover {
            background-color: #0056c7;
            transform: translateY(-2px);
        }

        .page-title {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .page-title p {
            font-size: 1.1rem;
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
        }

        .media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .media-card {
            background-color: var(--card-bg);
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .media-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary);
        }

        .media-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .media-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .media-icon {
            width: 50px;
            height: 50px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: white;
            font-size: 1.5rem;
        }

        .media-info h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .media-info .date {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .media-content {
            margin-bottom: 1.5rem;
        }

        .media-content p {
            margin: 0;
            color: var(--text-color);
            line-height: 1.6;
        }

        .media-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border: 2px solid var(--primary);
            border-radius: var(--radius);
            transition: all 0.3s ease;
            background: transparent;
        }

        .media-link:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .stats-section {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 2rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .stat-item p {
            color: #6c757d;
            margin: 0.5rem 0 0 0;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .page-title h1 {
                font-size: 2rem;
            }

            .media-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .media-card {
                padding: 1.5rem;
            }

            .header__container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        .bi {
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <section class="header">
            <div class="header__container">
                <a class="logo" href="">
                    <img src="../assets/img/logo.png" alt="Логотип" />
                </a>
                <div class="right">
                    <a class="back" href="./gallery_video.php">
                        <i class="bi bi-arrow-left"></i>
                        Назад
                    </a>
                </div>
            </div>
        </section>

        <section class="page-title">
            <h1>СМИ о нас</h1>
            <p>Публикации и упоминания нашей организации в средствах массовой информации</p>
        </section>

        <?php
        // Подсчитываем статистику
        $total_publications = mysqli_num_rows($result);
        $unique_media = mysqli_num_rows(mysqli_query($conn, "SELECT DISTINCT media_name FROM media_publications"));
        $estimated_views = $total_publications * 3000; // Примерная оценка просмотров
        
        // Сбрасываем указатель результата для повторного использования
        mysqli_data_seek($result, 0);
        ?>
        <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <h3><?= $total_publications ?></h3>
                    <p>Публикаций</p>
                </div>
                <div class="stat-item">
                    <h3><?= $unique_media ?></h3>
                    <p>СМИ</p>
                </div>
                <div class="stat-item">
                    <h3><?= number_format($estimated_views) ?>+</h3>
                    <p>Просмотров</p>
                </div>
            </div>
        </section>

        <section class="media-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <?php
                    // Определяем иконку в зависимости от типа СМИ
                    $icon = 'bi-newspaper'; // по умолчанию
                    $link_text = 'Читать';
                    
                    switch ($row['media_type']) {
                        case 'tv':
                            $icon = 'bi-tv';
                            $link_text = 'Смотреть';
                            break;
                        case 'radio':
                            $icon = 'bi-megaphone';
                            $link_text = 'Слушать';
                            break;
                        case 'online':
                            $icon = 'bi-globe';
                            $link_text = 'Читать';
                            break;
                        case 'magazine':
                            $icon = 'bi-book';
                            $link_text = 'Читать';
                            break;
                        default:
                            $icon = 'bi-newspaper';
                            $link_text = 'Читать';
                    }
                    ?>
                    <article class="media-card">
                        <div class="media-header">
                            <div class="media-icon">
                                <i class="bi <?= $icon ?>"></i>
                            </div>
                            <div class="media-info">
                                <h3><?= htmlspecialchars($row['media_name']) ?></h3>
                                <div class="date"><?= date('d.m.Y', strtotime($row['publication_date'])) ?></div>
                            </div>
                        </div>
                        <div class="media-content">
                            <p><?= htmlspecialchars($row['description']) ?></p>
                        </div>
                        <?php if (!empty($row['link'])): ?>
                            <a href="<?= htmlspecialchars($row['link']) ?>" class="media-link" target="_blank">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <?= $link_text ?>
                            </a>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-newspaper"></i>
                    <h3>Публикации не найдены</h3>
                    <p>Пока нет публикаций в СМИ о нашей организации</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>

</html>
