<?php

namespace Services;

use Exceptions\AssetsServiceException;

class AssetsService
{
    const IMAGES_DIR = BASE_PATH . 'assets/img/';
    const VIDEOS_DIR = BASE_PATH . 'assets/video/';
    const AUDIOS_DIR = BASE_PATH . 'assets/audio/';

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
            case 'webp':
                return 'image/webp';
            default:
                return null;
        }
    }

    public static function getVideoContentType(string $extension): ?string
    {
        switch (strtolower($extension)) {
            case 'mp4':
                return 'video/mp4';
            case 'webm':
                return 'video/webm';
            case 'ogg':
                return 'video/ogg';
            default:
                return null;
        }
    }

    public static function getAudioContentType(string $extension): ?string
    {
        switch (strtolower($extension)) {
            case 'mp3':
                return 'audio/mpeg';
            case 'ogg':
                return 'audio/ogg';
            case 'wav':
                return 'audio/wav';
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

    /**
     * Récupère tous les fichiers images présents dans un dossier spécifique et retourne leurs noms et extensions.
     * @param string $folder
     * @throws AssetsServiceException
     * @return array{extension: array|string, name: string[]}
     */
    public static function getAllImagesRelativeInFolder(string $folder): array
    {
        $dir = self::IMAGES_DIR . $folder;
        if(!is_dir($dir)) {
            throw new AssetsServiceException("Le dossier spécifié ({$dir}) n'existe pas.");
        }
        $images = [];
        foreach (scandir($dir) as $file) {
            if (is_file("{$dir}/{$file}")) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $fileNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
                $images[] = ['name' => $folder . '/' . $fileNameWithoutExt, 'extension' => $extension];
            }
        }
        return $images;
    }

    public static function getVideoContent(string $path, string $extension): ?string
    {
        $file = self::VIDEOS_DIR . $path . '.' . $extension;
        if(!file_exists($file)) {
            dd('File not found: ' . $file);
            return null;
        }
        self::setRangeForAudioVideoFiles($file);
        return file_get_contents($file);
    }

    public static function getAudioContent(string $path, string $extension): ?string
    {
        $file = self::AUDIOS_DIR . $path . '.' . $extension;
        if(!file_exists($file)) {
            dd('File not found: ' . $file);
            return null;
        }
        self::setRangeForAudioVideoFiles($file);
        return file_get_contents($file);
    }

    private static function setRangeForAudioVideoFiles(string $filePath){
        $size = filesize($filePath);
        $fp = fopen($filePath, "rb");
        $start = 0;
        $end = $size - 1;
        header("Accept-Ranges: bytes");
        // gestion du header Range
        if (isset($_SERVER["HTTP_RANGE"])) {
            preg_match('/bytes=(\d+)-(\d*)/', $_SERVER["HTTP_RANGE"], $matches);
            $start = intval($matches[1]);
            if (!empty($matches[2])) {
                $end = intval($matches[2]);
            }
            header("HTTP/1.1 206 Partial Content");
            header("Content-Range: bytes $start-$end/$size");
        }
        $length = $end - $start + 1;
        header("Content-Length: $length");
        fseek($fp, $start);
        $buffer = 8192;
        while (!feof($fp) && ftell($fp) <= $end) {
            echo fread($fp, min($buffer, $end - ftell($fp) + 1));
            flush();
        }
        fclose($fp);
    }

    public static function getAllVideosRelativeInFolder(string $folder): array
    {
        $dir = self::VIDEOS_DIR . $folder;
        if(!is_dir($dir)) {
            throw new AssetsServiceException("Le dossier spécifié ({$dir}) n'existe pas.");
        }
        $videos = [];
        foreach (scandir($dir) as $file) {
            if (is_file("{$dir}/{$file}")) {
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $fileNameWithoutExt = pathinfo($file, PATHINFO_FILENAME);
                $videos[] = ['name' => $folder . '/' . $fileNameWithoutExt, 'extension' => $extension];
            }
        }
        return $videos;
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