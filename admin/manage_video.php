<?php
session_start();
include '../config.php';

$upload_dir = __DIR__ . '/uploads/videos/';
$public_upload_dir = '/admin/uploads/videos/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

// Добавление видео
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_video'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $url = !empty($_POST['url']) ? mysqli_real_escape_string($conn, $_POST['url']) : NULL;
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $tags = mysqli_real_escape_string($conn, $_POST['tags']);
    $file_path = NULL;

    if (!empty($_FILES['video_file']['name'])) {
        $file_name = time() . '_' . preg_replace('/\s+/', '_', basename($_FILES['video_file']['name']));
        $target_file = $upload_dir . $file_name;
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['mp4', 'mov', 'avi', 'mkv'];
        if (in_array($ext, $allowed) && move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
            $file_path = $public_upload_dir . $file_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO videos (title, url, file_path, category, tags) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $url, $file_path, $category, $tags);
    $stmt->execute();
    $_SESSION['message'] = 'Видео успешно добавлено!';
    $stmt->close();
    header("Location: manage_video.php");
    exit;
}

// Удаление
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $result = $conn->query("SELECT file_path FROM videos WHERE id = $id");
    $row = $result->fetch_assoc();
    if ($row && $row['file_path']) {
        $real = __DIR__ . $row['file_path'];
        if (file_exists($real)) unlink($real);
    }
    $conn->query("DELETE FROM videos WHERE id = $id");
    $_SESSION['message'] = 'Видео удалено!';
    header("Location: manage_video.php");
    exit;
}

$videos = $conn->query("SELECT * FROM videos ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="ru" data-theme="light">

<head>
    <meta charset="UTF-8">
    <title>Видеоархив</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <script src="https://kit.fontawesome.com/a2e0e9e6e0.js" crossorigin="anonymous"></script>
    <style>
        body[data-theme="dark"] {
            background: #121212;
            color: #eee;
        }

        body[data-theme="dark"] .box,
        body[data-theme="dark"] .table {
            background: #1f1f1f;
            color: #eee;
        }

        .preview-thumb {
            width: 100px;
            cursor: pointer;
        }

        .modal-content video,
        .modal-content iframe {
            width: 100%;
        }
    </style>
</head>

<body>
    <section class="section">
        <div class="container">
            <h1 class="title">📽 Управление видеоархивом</h1>

            <?php if (!empty($_SESSION['message'])): ?>
                <div class="notification is-success"><?= $_SESSION['message'];
                                                        unset($_SESSION['message']); ?></div>
            <?php endif; ?>

            <div class="field is-grouped is-justify-content-space-between">
                <div class="control">
                    <button class="button is-small" onclick="toggleTheme()">🌙 Переключить тему</button>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" class="box mt-3">
                <div class="field"><label class="label">Название</label>
                    <div class="control"><input class="input" name="title" required></div>
                </div>
                <div class="field"><label class="label">Категория</label>
                    <div class="control"><input class="input" name="category" placeholder="напр. События"></div>
                </div>
                <div class="field"><label class="label">Теги</label>
                    <div class="control"><input class="input" name="tags" placeholder="через запятую"></div>
                </div>
                <div class="field"><label class="label">YouTube-ссылка</label>
                    <div class="control"><input class="input" name="url" placeholder="https://youtube.com/..."></div>
                </div>
                <div class="field"><label class="label">Или загрузите видеофайл</label>
                    <div class="control"><input type="file" name="video_file" accept="video/*"></div>
                </div>
                <div class="control mt-3"><button name="add_video" class="button is-primary">Добавить</button></div>
            </form>

            <table class="table is-fullwidth is-striped mt-4">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Категория</th>
                        <th>Теги</th>
                        <th>Предпросмотр</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($v = $videos->fetch_assoc()): ?>
                        <tr>
                            <td><?= $v['id'] ?></td>
                            <td><?= htmlspecialchars($v['title']) ?></td>
                            <td><?= htmlspecialchars($v['category']) ?></td>
                            <td><?= htmlspecialchars($v['tags']) ?></td>
                            <td>
                                <?php if ($v['url']): ?>
                                    <img src="https://img.youtube.com/vi/<?= getYoutubeId($v['url']) ?>/mqdefault.jpg" class="preview-thumb" onclick="openModal('youtube', '<?= $v['url'] ?>')">
                                <?php elseif ($v['file_path']): ?>
                                    <video src="<?= $v['file_path'] ?>" class="preview-thumb" onclick="openModal('video', '<?= $v['file_path'] ?>')" muted></video>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?delete=<?= $v['id'] ?>" class="button is-danger is-small" onclick="return confirm('Удалить видео?')">Удалить</a>
                                <a href="edit_video.php?id=<?= $v['id'] ?>" class="button is-warning is-small">Редактировать</a>

                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal" id="videoModal">
        <div class="modal-background" onclick="closeModal()"></div>
        <div class="modal-content" id="modalContent"></div>
        <button class="modal-close is-large" aria-label="close" onclick="closeModal()"></button>
    </div>

    <script>
        function getYoutubeEmbed(url) {
            const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/);
            return match ? `https://www.youtube.com/embed/${match[1]}?autoplay=1` : '';
        }

        function openModal(type, src) {
            const content = document.getElementById('modalContent');
            if (type === 'youtube') {
                content.innerHTML = `<iframe src="${getYoutubeEmbed(src)}" frameborder="0" allowfullscreen></iframe>`;
            } else {
                content.innerHTML = `<video src="${src}" controls autoplay></video>`;
            }
            document.getElementById('videoModal').classList.add('is-active');
        }

        function closeModal() {
            document.getElementById('videoModal').classList.remove('is-active');
            document.getElementById('modalContent').innerHTML = '';
        }

        function toggleTheme() {
            const root = document.documentElement;
            root.setAttribute('data-theme', root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
        }
    </script>
</body>

</html>

<?php
function getYoutubeId($url)
{
    preg_match('/(?:v=|be\/)([^&]+)/', $url, $matches);
    return $matches[1] ?? '';
}
?>