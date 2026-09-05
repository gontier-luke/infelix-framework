<?php

namespace Controllers;

use Override\ControllerOverride;
use Services\AssetsService;

class AssetsController extends ControllerOverride
{
    protected string $name;
    const SCRIPT_DIR = BASE_PATH . 'assets/scripts/';
    const STYLE_DIR = BASE_PATH . 'build/css/';

    public function __construct()
    {
    }

    # [Route('/style/{path}.css', 'app_media_css')]
    public function css(string $path): bool
    {
        $file = self::STYLE_DIR . $path . '.css';
        $this->setTitle('CSS : ' . $path);
        if(!file_exists($file)) {
            dd('File not found: ' . $file);
            return false;
        }
        header('Content-Type: text/css');
        echo file_get_contents($file);
        return true;
    }

    # [Route('/script/{path}.js', 'app_media_script')]
    public function script(string $path): bool
    {
        $file = self::SCRIPT_DIR . $path . '.js';
        $this->setTitle('Script : ' . $path);
        if(!file_exists($file)) {
            dd('File not found: ' . $file);
            return false;
        }
        header('Content-Type: text/javascript');
        echo file_get_contents($file);
        return true;
    }

    # [Route('/image/{path}.{extension}', 'app_media_image')]
    public function image(string $path, string $extension): bool
    {
        $file = $path . '.' . $extension;
        $this->setTitle('Image : ' . $file);
        $contentType = AssetsService::getImageContentType($extension);
        if($contentType === null) {
            dd('Unsupported image extension: ' . $extension);
            return false;
        }
        $content = AssetsService::getImageContent($path, $extension);
        if($content === null) {
            return false;
        }
        header('Content-Type: image/'.$contentType);
        echo $content;
        return true;
    }

    # [Route('/ressources/{path}.{extension}', 'app_media_build')]
    public function build(string $path, string $extension): bool
    {
        $file = BASE_PATH . 'build/' . $path . '.' . $extension;
        $this->setTitle('Build Asset : ' . $path);
        if(!file_exists($file)) {
            dd('File not found: ' . $file);
            return false;
        }
        switch (strtolower($extension)) {
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'js':
                header('Content-Type: text/javascript');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            case 'jpg':
            case 'jpeg':
                header('Content-Type: image/jpeg');
                break;
            case 'svg':
                header('Content-Type: image/svg+xml');
                break;
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
            default:
                dd('Unsupported build asset extension: ' . $extension);
                return false;
        }
        echo file_get_contents($file);
        return true;
    }

    # [Route('/media/video/{path}.{extension}', 'app_media_video')]
    public function video(string $path, string $extension): bool
    {
        $file = $path . '.' . $extension;
        $this->setTitle('Vidéo : ' . $file);
        $contentType = AssetsService::getVideoContentType($extension);
        if($contentType === null) {
            dd('Unsupported video extension: ' . $extension);
            return false;
        }
        $content = AssetsService::getVideoContent($path, $extension);
        if($content === null) {
            return false;
        }
        header('Content-Type: ' . $contentType);
        header("Accept-Ranges: bytes");
        echo $content;
        return true;
    }

    # [Route('/media/audio/{path}.{extension}', 'app_media_audio')]
    public function audio(string $path, string $extension): bool
    {
        $file = $path . '.' . $extension;
        $this->setTitle('Audio : ' . $file);
        $contentType = AssetsService::getAudioContentType($extension);
        if($contentType === null) {
            dd('Unsupported audio extension: ' . $extension);
            return false;
        }
        $content = AssetsService::getAudioContent($path, $extension);
        if($content === null) {
            return false;
        }
        header('Content-Type: ' . $contentType);
        header("Accept-Ranges: bytes");
        echo $content;
        return true;
    }
}