<?php

function veteran_museum_root(): string
{
    return dirname(__DIR__);
}

function veteran_photo_save_dir(): ?string
{
    $root = veteran_museum_root();
    $candidates = [
        $root . '/admin/veterans/assets/images',
        $root . '/user-uploads/veterans',
    ];

    foreach ($candidates as $dir) {
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            continue;
        }
        if (!is_writable($dir)) {
            @chmod($dir, 0775);
        }
        if (is_writable($dir)) {
            return $dir;
        }
    }

    return null;
}

function veteran_photo_src(string $filename): string
{
    $filename = basename(str_replace('\\', '/', $filename));
    $root = veteran_museum_root();
    $folder = is_file($root . '/user-uploads/veterans/' . $filename)
        ? 'user-uploads/veterans/'
        : 'admin/veterans/assets/images/';

    return '../' . $folder . rawurlencode($filename);
}
