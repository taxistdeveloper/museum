<?php
if (!isset($conn)) {
    require_once dirname(__DIR__) . '/config.php';
}

$museum_root = realpath(dirname(__DIR__));
$current_dir = realpath(dirname($_SERVER['SCRIPT_FILENAME']));
$museum_base = ($current_dir && $museum_root && $current_dir === $museum_root) ? '' : '../';

if (!isset($museum_show_back)) {
    $museum_show_back = true;
}
if (!isset($museum_show_langs)) {
    $museum_show_langs = true;
}
if (empty($museum_back_href)) {
    $museum_back_href = $museum_base . 'index.php?lang=' . urlencode($language);
}
if (empty($museum_page_title)) {
    $museum_page_title = $lang['site_title'] ?? 'Museum KTSK';
}

if (!function_exists('museum_lang_url')) {
    function museum_lang_url($code)
    {
        $params = $_GET;
        $params['lang'] = $code;
        return '?' . http_build_query($params);
    }
}

if (!function_exists('museum_home_url')) {
    function museum_home_url()
    {
        global $museum_base, $language;
        return $museum_base . 'index.php?lang=' . urlencode($language);
    }
}
