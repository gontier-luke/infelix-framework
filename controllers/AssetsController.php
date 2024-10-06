<?php

class AssetsController extends ControllerCore
{
    protected string $name;
    const IMAGES_DIR = BASE_PATH . 'assets/img/';
    const SCRIPT_DIR = BASE_PATH . 'assets/scripts/';

    public function __construct()
    {
    }

    #[Route('/script/{path}.js', 'app_media_script')]
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

    #[Route('/image/{path}.{extension}', 'app_media_image')]
    public function image(string $path, string $extension): bool
    {
        $file = self::IMAGES_DIR . $path . '.' . $extension;
        $this->setTitle('Image : ' . $path);
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $extension = 'jpeg';
                break;
            case 'svg':
                $extension = 'svg+xml';
                break;
            case 'png':
            case 'gif':
                break;
            default:
                return false;
        }
        if(!file_exists($file)) {
            dd('File not found: ' . $file);
            return false;
        }
        header('Content-Type: image/'.$extension);
        echo file_get_contents($file);
        return true;
    }

}
