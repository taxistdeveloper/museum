<?php
include 'config.php';
$query_videos = "SELECT * FROM videos ORDER BY created_at DESC";
$result = mysqli_query($conn, $query_videos);
$videos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['category'] = $row['category'] ?? 'Общее';
    $row['tags'] = explode(',', $row['tags'] ?? '');
    $videos[] = $row;
}
$categories = array_unique(array_column($videos, 'category'));
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>🎬 Видео Архив</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
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
            --shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            transition: 0.3s;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 2rem 1rem;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        input[type="text"],
        select {
            padding: 0.7rem 1rem;
            border-radius: var(--radius);
            border: 1px solid #ccc;
            font-size: 1rem;
            width: 250px;
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .video-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: 0.3s;
            cursor: pointer;
            position: relative;
        }

        .video-card:hover {
            transform: translateY(-4px);
        }

        .video-thumb {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #ddd;
        }

        .video-info {
            padding: 1rem;
        }

        .video-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .video-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .video-tags .tag {
            background: #1d4ed8;
            color: #ffffff;
            padding: 0.2rem 0.5rem;
            border-radius: 999px;
            font-size: 0.8rem;
        }

        .label-new {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ff3860;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: bold;
            z-index: 10;
        }

        .back-button {
            margin-top: 3rem;
            display: flex;
            justify-content: center;
        }

        .back-button a {
            text-decoration: none;
            background: var(--primary);
            color: #fff;
            padding: 0.7rem 1.4rem;
            border-radius: var(--radius);
            font-weight: 500;
            transition: 0.3s;
        }

        .back-button a:hover {
            background-color: #0056c7;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.75);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--card-bg);
            padding: 1rem;
            border-radius: var(--radius);
            max-width: 800px;
            width: 90%;
        }

        .modal-content iframe,
        .modal-content video {
            width: 100%;
            height: 450px;
            border-radius: var(--radius);
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #fff;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            border-radius: 50%;
            padding: 0.2rem 0.6rem;
        }

        @media (max-width: 600px) {

            .modal-content iframe,
            .modal-content video {
                height: 280px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>🎬 Видео Архив</h1>

        <div class="filters">
            <input type="text" id="searchInput" placeholder="🔍 Поиск по названию...">
            <select id="categoryFilter">
                <option value="">Все категории</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= strtolower($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="video-grid" id="videoList">
            <?php foreach ($videos as $video): ?>
                <?php
                $isYoutube = !empty($video['url']);
                $src = $isYoutube ? $video['url'] : $video['file_path'];
                preg_match('/(?:v=|\/)([0-9A-Za-z_-]{11})/', $video['url'], $yt);
                $yt_id = $yt[1] ?? '';
                $isNew = strtotime($video['created_at']) > strtotime('-1 days');
                ?>
                <div class="video-card video-entry"
                    data-title="<?= strtolower($video['title']) ?>"
                    data-category="<?= strtolower($video['category']) ?>"
                    onclick="openModal('<?= $isYoutube ? 'youtube' : 'local' ?>', '<?= $src ?>')">

                    <?php if ($isNew): ?>
                        <div class="label-new">Новый</div>
                    <?php endif; ?>

                    <div class="video-thumb">
                        <?php if ($isYoutube): ?>
                            <img src="https://img.youtube.com/vi/<?= $yt_id ?>/mqdefault.jpg" class="video-thumb">
                        <?php elseif (!empty($video['file_path']) && file_exists(__DIR__ . $video['file_path'])): ?>
                            <video muted preload="metadata" class="video-thumb">
                                <source src="<?= $video['file_path'] ?>" type="video/mp4">
                            </video>
                        <?php else: ?>
                            <div class="video-thumb" style="background: #ccc; display:flex; align-items:center; justify-content:center; color:#888;">
                                Нет видео
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="video-info">
                        <div class="video-title"><?= htmlspecialchars($video['title']) ?></div>
                        <div class="video-tags">
                            <?php foreach ($video['tags'] as $tag): ?>
                                <span class="tag"><?= trim($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="back-button">
            <a href="../pages/gallery_video.php">← Назад</a>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="videoModal">
        <div class="modal-content" id="modalContent"></div>
        <button class="modal-close" onclick="closeModal()">×</button>
    </div>

    <script>
        function openModal(type, src) {
            const content = document.getElementById('modalContent');
            if (type === 'youtube') {
                const match = src.match(/(?:v=|\/)([0-9A-Za-z_-]{11})/);
                const id = match ? match[1] : '';
                content.innerHTML = `<iframe src="https://www.youtube.com/embed/${id}?autoplay=1" frameborder="0" allowfullscreen></iframe>`;
            } else {
                content.innerHTML = `<video src="${src}" controls autoplay></video>`;
            }
            document.getElementById('videoModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('videoModal').classList.remove('active');
            document.getElementById('modalContent').innerHTML = '';
        }

        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');

        function filterVideos() {
            const query = searchInput.value.toLowerCase();
            const category = categoryFilter.value.toLowerCase();
            document.querySelectorAll('.video-entry').forEach(entry => {
                const matchText = entry.dataset.title.includes(query);
                const matchCat = !category || entry.dataset.category === category;
                entry.style.display = matchText && matchCat ? '' : 'none';
            });
        }

        searchInput.addEventListener('input', filterVideos);
        categoryFilter.addEventListener('change', filterVideos);
    </script>

</body>

</html>