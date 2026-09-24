<?php
session_start();
include '../config.php';

$is_logged_in = isset($_SESSION['user_id']);
$query = "SELECT * FROM history ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Удаление
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $delete_query = "DELETE FROM history WHERE id = $delete_id";
    if (mysqli_query($conn, $delete_query)) {
        header("Location: ./history.php?message=deleted");
        exit;
    } else {
        echo "Ошибка: " . mysqli_error($conn);
    }
}

// Сообщения
$message = '';
if (isset($_GET['message'])) {
    $msgMap = [
        'success' => 'История обновлена успешно!',
        'deleted' => 'История удалена успешно!'
    ];
    $message = $msgMap[$_GET['message']] ?? '';
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>История колледжа</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f9fafb;
            --card: #ffffff;
            --text: #1f1f1f;
            --primary: #007bff;
            --danger: #dc3545;
            --radius: 12px;
            --shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .header__container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            padding: 1rem 0;
        }

        .logo img {
            max-height: 140px;
        }

        .back {
            background-color: var(--primary);
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .back:hover {
            background-color: #0056b3;
        }

        .title__page {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }

        .notification {
            padding: 1rem;
            border-radius: var(--radius);
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            margin-bottom: 1.5rem;
            transition: opacity 0.5s ease;
        }

        .timeline {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .timeLine-item {
            display: flex;
            flex-direction: column;
            background: var(--card);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            opacity: 0;
            transform: translateY(20px);
            transition: 0.6s ease-in-out;
        }

        .timeLine-item.active {
            opacity: 1;
            transform: translateY(0);
        }

        .check {
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }

        .date {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .text {
            font-size: 1rem;
            line-height: 1.6;
        }

        .timeline-footer {
            margin-top: 1rem;
            display: flex;
            gap: 10px;
        }

        .button {
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
        }

        .is-info {
            background-color: #17a2b8;
            color: white;
        }

        .is-info:hover {
            background-color: #138496;
        }

        .is-danger {
            background-color: var(--danger);
            color: white;
        }

        .is-danger:hover {
            background-color: #c82333;
        }

        .is-primary {
            background-color: var(--primary);
            color: white;
        }

        .is-primary:hover {
            background-color: #0069d9;
        }

        /* Start section History */
        }

        .History .history__container {
            position: relative;
        }



        .History .timeline {
            margin-top: 40px;
        }

        .History .timeline::before {
            background: #dadee4;
            content: "";
            height: 100%;
            left: 19px;
            position: absolute;
            top: 90px;
            width: 2px;
            z-index: -1;
        }

        .History .timeLine-item {
            width: 90%;
            border-radius: 20px;
            background: #fff;
            margin-bottom: 40px;
            margin-left: 50px;
            position: relative;
            height: auto;
            padding: 30px;
            box-sizing: border-box;
        }

        .History .timeLine-item .check {
            background: #063498;
            width: 50px;
            height: 50px;
            border-radius: 100%;
            top: 0;
            position: absolute;
            left: -55px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            font-size: 30px;
        }

        .History .timeLine-item .date {
            margin-bottom: 20px;
            font-weight: 800;
            font-size: 28px;
        }

        .History .timeLine-item .content {
            width: 100%;
        }

        .History .timeLine-item .content img {
            width: 400px;
            border-radius: 20px;
            object-fit: contain;
            float: left;
            margin-right: 30px;
        }

        /* End section History */
        @media (max-width: 768px) {
            .timeline-footer {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <section class="header">
            <div class="header__container">
                <a class="logo" href="/">
                    <img src="https://ff2.object.pscloud.io/giwycqkw/oeqsaqye/eoamgqcg/4384bf6eeeace361ca86ed2a4e97d2975c665532.png" alt="Логотип">
                </a>
                <a class="back" href="../index.php">Назад</a>
            </div>
        </section>

        <section class="History">
            <div class="history__container">
                <div class="title__page">История колледжа в значимых событиях</div>

                <?php if ($message): ?>
                    <div id="success-message" class="notification"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>

                <div class="timeline">
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="timeLine-item">
                            <div class="check"><i class="bi bi-bookmark-check-fill"></i></div>
                            <div class="date"><?= htmlspecialchars($row['title']) ?></div>
                            <div class="content">
                                <div class="text"><?= nl2br(htmlspecialchars($row['content'])) ?></div>
                                <?php if ($is_logged_in): ?>
                                    <div class="timeline-footer">
                                        <a href="../admin/history/edit_history.php?id=<?= $row['id'] ?>" class="button is-info">Редактировать</a>
                                        <a href="?delete_id=<?= $row['id'] ?>" class="button is-danger" onclick="return confirm('Удалить эту историю?')">Удалить</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php if ($is_logged_in): ?>
                    <div style="margin-top: 2rem;">
                        <a href="../admin/history/add_history.php" class="button is-primary">Добавить историю</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <script>
        // Плавное исчезновение сообщения
        const msg = document.getElementById('success-message');
        if (msg) {
            setTimeout(() => {
                msg.style.opacity = '0';
                setTimeout(() => msg.remove(), 600);
            }, 2000);
        }

        // Анимация появления
        document.addEventListener('DOMContentLoaded', () => {
            const items = document.querySelectorAll('.timeLine-item');
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, {
                threshold: 0.4
            });

            items.forEach(item => observer.observe(item));
        });
    </script>
</body>

</html>