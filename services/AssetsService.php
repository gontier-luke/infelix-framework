<?php

namespace Services;
class AssetsService
{
    const IMAGES_DIR = BASE_PATH . 'assets/img/';

    public static function getImageContentType(string $extension): ?string
    {
        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            case 'svg':
                return 'image/svg+xml';
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
            default:
                return null;
        }
    }

    public static function getImageContent(string $path, string $extension): ?string
    {
        $file = self::IMAGES_DIR . $path . '.' . $extension;
        if(!file_exists($file)) {
            dd('File not found: ' . $file);
            return null;
        }
        return file_get_contents($file);
    }
}