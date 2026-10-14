<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/init.php';

function museum_ascii_digits(string $text): string
{
    return preg_replace_callback('/\p{Nd}/u', static function (array $match): string {
        $digit = $match[0];
        if (preg_match('/^[0-9]$/', $digit)) {
            return $digit;
        }
        $ord = mb_ord($digit);
        foreach ([0x1D7CE, 0x1D7D8, 0x1D7E2, 0x1D7EC, 0x1D7F6] as $base) {
            if ($ord >= $base && $ord < $base + 10) {
                return (string) ($ord - $base);
            }
        }
        if ($ord >= 0xFF10 && $ord <= 0xFF19) {
            return (string) ($ord - 0xFF10);
        }
        return $digit;
    }, $text) ?? $text;
}

function museum_study_chips(string $study): array
{
    if ($study === '') {
        return [];
    }

    $chips = [];
    if (preg_match_all('/(\d+)\s*кл\.?/u', $study, $classes)) {
        foreach ($classes[1] as $grade) {
            $chips[] = $grade . ' кл.';
        }
    }
    if (preg_match_all('/(\d+)\s*г\.?\s*(\d+)\s*мес/u', $study, $durations, PREG_SET_ORDER)) {
        foreach ($durations as $duration) {
            $chips[] = $duration[1] . ' г. ' . $duration[2] . ' мес.';
        }
    }
    if (preg_match('/бюджет/iu', $study)) {
        $chips[] = 'бюджет';
    }
    if (preg_match('/\(([^)]*(?:каз|рус|қаз|eng)[^)]*)\)/iu', $study, $language)) {
        $chips[] = trim($language[1]);
    }
    if (preg_match('/заоч/iu', $study)) {
        $chips[] = 'заочная';
    }

    return array_values(array_unique($chips));
}

function museum_parse_specialty(string $raw): array
{
    $text = trim(preg_replace('/\s+/u', ' ', museum_ascii_digits($raw)) ?? '');
    $demand = '';
    $body = $text;
    if (preg_match('/^(.*?)\s*(?:востребованность|сұраныс|demand)\s*:\s*(.*)$/iu', $text, $parts)) {
        $body = trim($parts[1]);
        $demand = trim($parts[2]);
    }

    $quals = [];
    $study = '';
    if (preg_match_all('/(\d[A-Za-z]\d{5,})/u', $body, $codes, PREG_OFFSET_CAPTURE) && $codes[1]) {
        $found = $codes[1];
        $count = count($found);
        for ($i = 0; $i < $count; $i++) {
            $code = $found[$i][0];
            $start = $found[$i][1] + strlen($code);
            $end = ($i + 1 < $count) ? $found[$i + 1][1] : strlen($body);
            $chunk = trim(substr($body, $start, $end - $start));
            $title = $chunk;
            if ($i === $count - 1 && preg_match('/^(.*?)\s+(\d+\s*кл\b.*)$/u', $chunk, $tail)) {
                $title = trim($tail[1]);
                $study = trim($tail[2]);
            }
            if ($title !== '' || $code !== '') {
                $quals[] = ['code' => $code, 'title' => $title];
            }
        }
    }

    return [
        'quals' => $quals,
        'chips' => museum_study_chips($study),
        'demand' => $demand,
        'fallback' => $quals === [] ? $text : '',
    ];
}

function museum_specialty_icon(string $icon, string $name): string
{
    $icon = trim($icon);
    if ($icon !== '' && $icon !== 'bi-book' && preg_match('/^bi-[a-z0-9-]+$/', $icon)) {
        return $icon;
    }

    $normalized = mb_strtolower($name);
    $map = [
        'мебел' => 'bi-lamp',
        'промышленн' => 'bi-box-seam',
        'жилищн' => 'bi-wrench-adjustable',
        'инженерн' => 'bi-wrench-adjustable',
        'промысл' => 'bi-palette',
        'прикладн' => 'bi-palette',
        'ландшафт' => 'bi-tree',
        'свароч' => 'bi-fire',
        'реставрац' => 'bi-building',
        'недвижим' => 'bi-houses',
        'аэродром' => 'bi-signpost-2',
        'дорог' => 'bi-signpost-2',
        'автоматизир' => 'bi-pc-display',
        'сооружен' => 'bi-buildings',
        'безопасн' => 'bi-shield-lock',
        'архитектур' => 'bi-bezier2',
        'интерьер' => 'bi-house-heart',
        'электромехан' => 'bi-lightning-charge',
    ];
    foreach ($map as $needle => $class) {
        if (mb_strpos($normalized, $needle) !== false) {
            return $class;
        }
    }

    return 'bi-mortarboard';
}

$db = mysqli_real_escape_string($conn, $db_lang);
$query = "SELECT s.*, sl.name AS translated_name, sl.description AS translated_description
          FROM specialties s
          LEFT JOIN specialties_lang sl ON s.id = sl.specialty_id AND sl.language = '$db'
          ORDER BY s.created_at DESC";
$result = mysqli_query($conn, $query);
$museum_page_title = $lang['specialties'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($language) ?>">
<?php include __DIR__ . '/../includes/head.php'; ?>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>

<h1 class="museum-title"><?= htmlspecialchars($lang['specialties']) ?></h1>
<div class="specialty-grid">
    <?php while ($specialty = mysqli_fetch_assoc($result)): ?>
        <?php
        $name = (string) ($specialty['translated_name'] ?? $specialty['name']);
        $description = (string) ($specialty['translated_description'] ?? $specialty['description']);
        $parsed = museum_parse_specialty($description);
        $icon = museum_specialty_icon((string) ($specialty['icon'] ?? ''), $name);
        ?>
        <article class="museum-card specialty-card">
            <header class="specialty-card__head">
                <span class="specialty-card__icon" aria-hidden="true">
                    <i class="bi <?= htmlspecialchars($icon) ?>"></i>
                </span>
                <h2 class="specialty-card__title"><?= htmlspecialchars($name) ?></h2>
            </header>
            <div class="specialty-card__body">
                <?php if ($parsed['quals']): ?>
                    <section>
                        <p class="specialty-label"><?= htmlspecialchars($lang['specialty_qualifications']) ?></p>
                        <ul class="specialty-quals">
                            <?php foreach ($parsed['quals'] as $qual): ?>
                                <li>
                                    <span class="specialty-code"><?= htmlspecialchars($qual['code']) ?></span>
                                    <span class="specialty-qual"><?= htmlspecialchars($qual['title']) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>

                <?php if ($parsed['chips']): ?>
                    <section>
                        <p class="specialty-label"><?= htmlspecialchars($lang['specialty_study']) ?></p>
                        <div class="specialty-chips">
                            <?php foreach ($parsed['chips'] as $chip): ?>
                                <span class="specialty-chip"><?= htmlspecialchars($chip) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if ($parsed['demand'] !== ''): ?>
                    <section class="specialty-demand">
                        <p class="specialty-label"><?= htmlspecialchars($lang['specialty_demand']) ?></p>
                        <p><?= htmlspecialchars($parsed['demand']) ?></p>
                    </section>
                <?php elseif ($parsed['fallback'] !== ''): ?>
                    <p class="specialty-fallback"><?= nl2br(htmlspecialchars($parsed['fallback'])) ?></p>
                <?php endif; ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
