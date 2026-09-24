<?php
include '../config.php';
$query_students = "SELECT * FROM students ORDER BY LOWER(name) ASC";
$result_students = mysqli_query($conn, $query_students);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Наши Студенты</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s ease;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 2rem 1rem;
        }

        .header {
            background: var(--primary);
            padding: 1.5rem;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .title {
            font-size: 2rem;
            font-weight: 700;
        }

        .back {
            color: #fff;
            text-decoration: none;
            background-color: rgba(0, 0, 0, 0.15);
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            transition: background-color 0.3s;
        }

        .back:hover {
            background-color: rgba(0, 0, 0, 0.25);
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            justify-content: center;
        }

        .card {
            background: var(--card-bg);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            border-radius: var(--radius);
            flex: 0 1 300px;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .student-img {
            width: 100%;
            max-width: 100px;
            border-radius: 8px;
            object-fit: cover;
        }

        .card-content {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .card-info h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .card-info p {
            font-size: 0.9rem;
            margin: 0.5rem 0;
        }

        .button {
            background-color: var(--primary);
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #0056c7;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal.active {
            display: flex;
        }

        .modal-card {
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: var(--radius);
            width: 90%;
            max-width: 600px;
            color: var(--text-color);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .modal-content {
            max-height: 300px;
            overflow-y: auto;
        }

        .theme-toggle {
            cursor: pointer;
            font-size: 1.4rem;
        }

        @media (max-width: 600px) {
            .card-content {
                flex-direction: column;
                text-align: center;
            }

            .card-info {
                text-align: center;
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

    <header class="header">
        <div class="title">Наши Студенты</div>
        <div>
            <a href="../index.php" class="back">← Назад</a>
            <span class="theme-toggle" onclick="toggleTheme()">🌓</span>
        </div>
    </header>

    <main class="container">
        <div class="grid">
            <?php while ($student = mysqli_fetch_assoc($result_students)): ?>
                <div class="card">
                    <div class="card-content">
                        <img class="student-img" src="../admin/students/uploads/<?= $student['image'] ?>" alt="<?= htmlspecialchars($student['name']) ?>">
                        <div class="card-info">
                            <h3><?= htmlspecialchars($student['name']) ?></h3>
                            <p><strong>Группа:</strong> <?= htmlspecialchars($student['group_name'] ?? '—') ?></p>
                            <button class="button" onclick="openModal(<?= $student['id'] ?>)">Достижения</button>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal" id="modal<?= $student['id'] ?>">
                    <div class="modal-card">
                        <div class="modal-header">
                            <h4>Достижения: <?= htmlspecialchars($student['name']) ?></h4>
                            <button onclick="closeModal(<?= $student['id'] ?>)">✖</button>
                        </div>
                        <div class="modal-content">
                            <?php
                            $achievements_raw = trim($student['achievements']);
                            $achievements_array = json_decode($achievements_raw, true);
                            if (is_array($achievements_array) && count($achievements_array) > 0):
                                $i = 1;
                                foreach ($achievements_array as $item):
                            ?>
                                    <div style="margin-bottom: 1rem;">
                                        <strong>🏅 Достижение <?= $i++ ?>:</strong><br>
                                        <?= nl2br(htmlspecialchars($item)) ?>
                                    </div>
                                <?php endforeach;
                            else: ?>
                                <p style="color: #888;"><em>Нет достижений</em></p>
                            <?php endif; ?>
                        </div>
                        <div style="text-align: right; margin-top: 1rem;">
                            <button class="button" onclick="closeModal(<?= $student['id'] ?>)">Закрыть</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script>
        function openModal(id) {
            document.getElementById('modal' + id).classList.add('active');
        }

        function closeModal(id) {
            document.getElementById('modal' + id).classList.remove('active');
        }
    </script>

</body>

</html>