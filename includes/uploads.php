<?php

function museum_root(): string
{
    return dirname(__DIR__);
}

function museum_upload_legacy(string $bucket): string
{
    $map = [
        'students' => '/admin/students/uploads',
        'teachers' => '/admin/teachers/uploads',
        'gallery' => '/admin/gallery/uploads',
        'videos' => '/admin/uploads/videos',
        'projects' => '/admin/projects/assets/images',
        'directors' => '/admin/directors/assets/images',
        'veterans' => '/admin/veterans/assets/images',
        'icons' => '/uploads/icons',
    ];

    return museum_root() . ($map[$bucket] ?? ('/user-uploads/' . $bucket));
}

function museum_upload_dir(string $bucket): ?string
{
    $legacy = museum_upload_legacy($bucket);
    if (is_dir($legacy) && is_writable($legacy)) {
        return $legacy;
    }

    $fallback = museum_root() . '/user-uploads/' . $bucket;
    if (!is_dir($fallback) && !@mkdir($fallback, 0775, true) && !is_dir($fallback)) {
        return null;
    }
    if (!is_writable($fallback)) {
        @chmod($fallback, 0775);
    }

    return is_writable($fallback) ? $fallback : null;
}

function museum_upload_web(string $bucket, string $filename): string
{
    $filename = basename(str_replace('\\', '/', $filename));
    $root = museum_root();
    $folder = ($filename !== '' && is_file($root . '/user-uploads/' . $bucket . '/' . $filename))
        ? 'user-uploads/' . $bucket
        : ltrim(substr(museum_upload_legacy($bucket), strlen($root)), '/');

    return $folder . '/' . rawurlencode($filename);
}

function museum_upload_src(string $bucket, string $filename): string
{
    $root = realpath(museum_root()) ?: museum_root();
    $script = $_SERVER['SCRIPT_FILENAME'] ?? '';
    $dir = $script !== '' ? dirname($script) : getcwd();
    $dir = realpath($dir) ?: $dir;
    $prefix = '../';

    if ($dir !== '' && str_starts_with($dir, $root)) {
        $rest = trim(substr($dir, strlen($root)), '/');
        $prefix = $rest === '' ? '' : str_repeat('../', substr_count($rest, '/') + 1);
    }

    return $prefix . museum_upload_web($bucket, $filename);
}

function museum_upload_abs(string $bucket, string $filename): ?string
{
    $filename = basename(str_replace('\\', '/', $filename));
    if ($filename === '') {
        return null;
    }

    $fallback = museum_root() . '/user-uploads/' . $bucket . '/' . $filename;
    if (is_file($fallback)) {
        return $fallback;
    }

    $legacy = museum_upload_legacy($bucket) . '/' . $filename;
    return is_file($legacy) ? $legacy : null;
}

function museum_upload_public(string $dir, string $bucket, string $filename): string
{
    $filename = basename(str_replace('\\', '/', $filename));
    if (str_contains($dir, '/user-uploads/' . $bucket)) {
        return '/museum/user-uploads/' . $bucket . '/' . $filename;
    }

    $root = museum_root();
    return '/' . ltrim(substr($dir, strlen($root)), '/') . '/' . $filename;
}
