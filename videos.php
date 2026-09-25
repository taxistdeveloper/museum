<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/init.php';

$result = mysqli_query($conn, "SELECT * FROM videos ORDER BY created_at DESC");
$videos = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['category'] = $row['category'] ?? 'Общее';
    $row['tags'] = explode(',', $row['tags'] ?? '');
    $videos[] = $row;
}
$categories = array_unique(array_column($videos, 'category'));
$museum_page_title = $lang['video_archive'];
$museum_back_href = 'pages/gallery_video.php?lang=' . urlencode($language);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/includes/head.php'; ?>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['video_archive']) ?></h1>

<div class="filters">
    <input type="text" id="searchInput" placeholder="<?= htmlspecialchars($lang['search_placeholder']) ?>">
    <select id="categoryFilter">
        <option value=""><?= htmlspecialchars($lang['all_categories']) ?></option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars(strtolower($cat)) ?>"><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<div class="video-grid" id="videoList">
    <?php foreach ($videos as $video): ?>
        <?php
        $isYoutube = !empty($video['url']);
        $src = $isYoutube ? $video['url'] : $video['file_path'];
        preg_match('/(?:v=|\/)([0-9A-Za-z_-]{11})/', (string)$video['url'], $yt);
        $yt_id = $yt[1] ?? '';
        $isNew = strtotime($video['created_at']) > strtotime('-1 days');
        ?>
        <div class="video-card video-entry"
             data-title="<?= htmlspecialchars(strtolower($video['title'])) ?>"
             data-category="<?= htmlspecialchars(strtolower($video['category'])) ?>"
             onclick="openModal('<?= $isYoutube ? 'youtube' : 'local' ?>', '<?= htmlspecialchars($src, ENT_QUOTES) ?>')">
            <?php if ($isNew): ?>
                <div class="label-new"><?= htmlspecialchars($lang['new_label']) ?></div>
            <?php endif; ?>
            <?php if ($isYoutube): ?>
                <img src="https://img.youtube.com/vi/<?= htmlspecialchars($yt_id) ?>/mqdefault.jpg"
                     class="video-thumb"
                     alt="<?= htmlspecialchars($video['title']) ?>"
                     loading="lazy">
            <?php elseif (!empty($video['file_path'])): ?>
                <video muted preload="metadata" class="video-thumb">
                    <source src="<?= htmlspecialchars($video['file_path']) ?>" type="video/mp4">
                </video>
            <?php else: ?>
                <div class="video-thumb" style="display:flex;align-items:center;justify-content:center;color:var(--muted);">
                    <?= htmlspecialchars($lang['no_video']) ?>
                </div>
            <?php endif; ?>
            <div class="video-info">
                <div class="video-title"><?= htmlspecialchars($video['title']) ?></div>
                <div class="video-tags">
                    <?php foreach ($video['tags'] as $tag): ?>
                        <?php if (trim($tag) !== ''): ?>
                            <span class="tag"><?= htmlspecialchars(trim($tag)) ?></span>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="museum-modal" id="videoModal" onclick="if(event.target===this)closeModal()">
    <div class="museum-modal__card" style="max-width:800px;">
        <div class="museum-modal__head">
            <span></span>
            <button class="museum-modal__close" type="button" onclick="closeModal()" aria-label="<?= htmlspecialchars($lang['close']) ?>">×</button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<script>
    function openModal(type, src) {
        const content = document.getElementById('modalContent');
        if (type === 'youtube') {
            const match = src.match(/(?:v=|\/)([0-9A-Za-z_-]{11})/);
            const id = match ? match[1] : '';
            content.innerHTML = '<iframe src="https://www.youtube.com/embed/' + id + '?autoplay=1" style="width:100%;height:420px;border:0;border-radius:12px;" allowfullscreen></iframe>';
        } else {
            content.innerHTML = '<video src="' + src + '" controls autoplay style="width:100%;border-radius:12px;"></video>';
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
        document.querySelectorAll('.video-entry').forEach(function (entry) {
            const matchText = entry.dataset.title.includes(query);
            const matchCat = !category || entry.dataset.category === category;
            entry.style.display = matchText && matchCat ? '' : 'none';
        });
    }
    searchInput.addEventListener('input', filterVideos);
    categoryFilter.addEventListener('change', filterVideos);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
