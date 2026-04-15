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

    # [Route('/build/{path}', 'app_media_build')]
    public function build(string $path): bool
    {
        $file = BASE_PATH . 'build/' . $path;
        $this->setTitle('Build Asset : ' . $path);
        if(!file_exists($file)) {
            dd('File not found: ' . $file);
            return false;
        }
        $extension = pathinfo($file, PATHINFO_EXTENSION);
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
            default:
                dd('Unsupported build asset extension: ' . $extension);
                return false;
        }
        echo file_get_contents($file);
        return true;
    }

}
