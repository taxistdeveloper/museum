<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>История</title>
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
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 1.5rem;
        }

        .header__container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
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
        }

        .back:hover {
            background-color: #0056c7;
        }

        .HistoryName {
            margin-top: 2rem;
        }

        .historyName__title {
            font-size: 1.8rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 2rem;
        }

        .grid {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .ContentBlock {
            flex: 1 1 240px;
            max-width: 300px;
            background-color: var(--card-bg);
            box-shadow: var(--shadow);
            border-radius: var(--radius);
            padding: 2rem;
            text-align: center;
            text-decoration: none;
            color: var(--text-color);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .ContentBlock:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .ContentBlock .icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            background-color: var(--primary);
            border-radius: 50%;
        }

        .ContentBlock .title {
            font-size: 1.1rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .grid {
                flex-direction: column;
                align-items: center;
            }
        }

        .bi {
            font-size: 44px;
            color: rgb(13, 110, 253);

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
                    <a class="back" href="../index.php">← Назад</a>
                </div>
            </div>
        </section>

        <section class="HistoryName">
            <div class="historyName__container">
                <div class="historyName__title">Мы – источник спроса на инновации</div>
                <div class="grid">
                    <a href="./insta_blog.php" class="ContentBlock">
                        <div class="bi bi-camera-fill"></div>
                        <div class="title">Фото архив</div>
                    </a>

                    <a href="../videos.php" class="ContentBlock">
                        <div class=" bi bi-youtube"></div>
                        <div class="title">Видео архив</div>
                    </a>

                    <a href="./media_about_us.php" class="ContentBlock">
                        <div class="bi bi-newspaper"></div>
                        <div class="title">СМИ о нас</div>
                    </a>
                </div>
            </div>
        </section>
    </div>
</body>

</html>