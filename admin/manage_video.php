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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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
    <section class="py-4">
        <div class="container">
            <h1 class="h2">📽 Управление видеоархивом</h1>

            <?php if (!empty($_SESSION['message'])): ?>
                <div class="alert alert-success"><?= $_SESSION['message'];
                                                        unset($_SESSION['message']); ?></div>
            <?php endif; ?>

            <div class="d-flex gap-2 flex-wrap justify-content-between mb-3">
                <a href="index.php" class="btn btn-light">Назад</a>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleTheme()">Переключить тему</button>
            </div>

            <form method="POST" enctype="multipart/form-data" class="card card-body mt-3">
                <div class="mb-3">
                    <label class="form-label">Название</label>
                    <input class="form-control" name="title" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Категория</label>
                    <input class="form-control" name="category" placeholder="напр. События">
                </div>
                <div class="mb-3">
                    <label class="form-label">Теги</label>
                    <input class="form-control" name="tags" placeholder="через запятую">
                </div>
                <div class="mb-3">
                    <label class="form-label">YouTube-ссылка</label>
                    <input class="form-control" name="url" placeholder="https://youtube.com/...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Или загрузите видеофайл</label>
                    <input class="form-control" type="file" name="video_file" accept="video/*">
                </div>
                <button name="add_video" class="btn btn-primary">Добавить</button>
            </form>

            <table class="table table-striped table-hover mt-4">
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
                                <a href="?delete=<?= $v['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Удалить видео?')">Удалить</a>
                                <a href="edit_video.php?id=<?= $v['id'] ?>" class="btn btn-warning btn-sm">Редактировать</a>

                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="videoModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Просмотр</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body" id="modalContent"></div>
            </div>
        </div>
    </div>

    <script>
        function getYoutubeEmbed(url) {
            const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/);
            return match ? `https://www.youtube.com/embed/${match[1]}?autoplay=1` : '';
        }

        function openModal(type, src) {
            const content = document.getElementById('modalContent');
            if (type === 'youtube') {
                content.innerHTML = `<iframe src="${getYoutubeEmbed(src)}" style="width:100%;height:420px;border:0;" allowfullscreen></iframe>`;
            } else {
                content.innerHTML = `<video src="${src}" controls autoplay style="width:100%;"></video>`;
            }
            bootstrap.Modal.getOrCreateInstance(document.getElementById('videoModal')).show();
        }

        document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('modalContent').innerHTML = '';
        });

        function toggleTheme() {
            const root = document.documentElement;
            root.setAttribute('data-theme', root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
function getYoutubeId($url)
{
    preg_match('/(?:v=|be\/)([^&]+)/', $url, $matches);
    return $matches[1] ?? '';
}
?>