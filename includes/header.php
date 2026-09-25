<div class="museum-wrap">
<header class="museum-header">
    <div class="museum-header__brand">
        <a class="museum-logo" href="<?= htmlspecialchars(museum_home_url()) ?>">
            <img src="<?= htmlspecialchars($museum_base) ?>assets/img/logo.png" alt="КТСК">
        </a>
        <?php if (!empty($museum_show_langs)): ?>
            <nav class="language-switcher" aria-label="Language">
                <a href="<?= htmlspecialchars(museum_lang_url('kk')) ?>" class="<?= $language === 'kk' ? 'active' : '' ?>">Қазақша</a>
                <a href="<?= htmlspecialchars(museum_lang_url('ru')) ?>" class="<?= $language === 'ru' ? 'active' : '' ?>">Русский</a>
                <a href="<?= htmlspecialchars(museum_lang_url('en')) ?>" class="<?= $language === 'en' ? 'active' : '' ?>">English</a>
            </nav>
        <?php endif; ?>
    </div>
    <div class="museum-header__actions">
        <?php if (!empty($museum_show_back)): ?>
            <a class="museum-back" href="<?= htmlspecialchars($museum_back_href) ?>">
                <i class="bi bi-arrow-left"></i> <?= htmlspecialchars($lang['back']) ?>
            </a>
        <?php endif; ?>
        <button type="button" class="theme-toggle" onclick="toggleTheme()" title="<?= htmlspecialchars($lang['theme_toggle'] ?? 'Theme') ?>">
            <i class="bi bi-moon-stars-fill" data-theme-icon></i>
        </button>
    </div>
</header>
<main class="museum-main">
