<?php

require_once __DIR__ . '/uploads.php';

function veteran_photo_save_dir(): ?string
{
    return museum_upload_dir('veterans');
}

function veteran_photo_src(string $filename): string
{
    return museum_upload_src('veterans', $filename);
}
