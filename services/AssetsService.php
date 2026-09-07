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

    public static function uploadTemporaryImage(string $tmpName, string $filename): bool
    {
        $destination = self::IMAGES_DIR . $filename;
        return move_uploaded_file($tmpName, $destination);
    }

    public static function saveImageFromBase64(string $imageBase64, string $filename): bool
    {
        $imageData = base64_decode($imageBase64);
        if ($imageData === false) {
            return false;
        }
        $destinationPath = self::IMAGES_DIR . $filename;

        return file_put_contents($destinationPath, $imageData) !== false;
    }
}