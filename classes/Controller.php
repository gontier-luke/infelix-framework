<?php
namespace Classes;

use Repositories\Configuration;
use Enum\LangEnum as Lang;
use Services\AssetsService;

class ControllerCore{
    protected string $name;
    protected string $template;
    protected array $stylesheets = [];
    protected array $scripts = [];
    protected string $title = 'Infelix Commentator - base';

    public static function getInstanceByName(string $name) : ?ControllerCore {
        
        $controllerPathes['base'] = BASE_PATH . '/controllers/';
        foreach(getAllActiveModules() as $module) {
            $moduleName = $module->getName();
            $moduleControllersPath = BASE_PATH . 'modules/' . $moduleName . '/controllers/';
            if(file_exists(BASE_PATH . 'modules/' . $moduleName . '/controllers/')) {
                $controllerPathes[$moduleName] = $moduleControllersPath;
            }

        }
        $controllerPath = $controllerPathes['base'];
        if (!file_exists($controllerPath)) {
            return null;
        }

        // On récupère le premier controller qui correspond au nom donné

        foreach($controllerPathes as $moduleName => $path) {
            $className =  ucfirst($name) .'Controller';
            if(file_exists($path . $className . '.php')) {
                require_once $path . $className .'.php';
                $className = '\Controllers\\'.$className;
                
                /** @var ControllerCore */  
                $controller = new $className();
                $controller->setName($name);
                $controller->addCSS('style');
                $controller->addJS('https://code.jquery.com/jquery-3.7.1.min.js');
                return $controller;
            }
        }

        return null;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    protected function addCSS($stylesheet): void
    {
        if (str_starts_with($stylesheet, 'http')) {
            $this->stylesheets[] = $stylesheet;
            return;
        }
        $this->stylesheets[] =  Router::generateUrl('app_media_css', ['path' => $stylesheet]);
    }

    protected function addJS($script): void
    {
        if (str_starts_with($script, 'http')) {
            $this->scripts[] = $script;
            return;
        }
        $this->scripts[] = Router::generateUrl('app_media_script', ['path' => $script]);
    }

    protected function setTitle($title): void
    {
        $this->title = $this->getSiteName() . ' - ' . $title;
    }

    protected function getSiteName(): string
    {
            return  Configuration::get('siteName');
    }

    /**
     * Récupération de l'icone de l'onglet
     * 
     * @return string
     */
    protected function getFavicon(): string
    {
        $path = Configuration::get('logo');
        $exploded = explode('.', $path);
        $extension = array_pop($exploded);
        $path = implode('.', $exploded);
        
        return   Router::generateUrl('app_media_image', ['path' => $path, 'extension' => $extension]);
    }
    
    /**
     * Liens du menu
     * 
     * @return array<string, string>
     */
    protected function setMenuLinks(): array
    {
        return [
        ];
    }

    /**
     * Liens du menu chanceux
     * @return array<string, string>
     */
    protected function getLuckyLink(): array
    {
        $luckyLink = [];

        if (rand(0, 854) === 42) {
            $luckyLink['Lucky link'] = Router::generateUrl(name: 'app_lucky'); // Remplacer par le site caché ^^
        }

        return $luckyLink;
    }

    /**
     * @return array<string, string>
     */
    protected function setFooterLinks(): array
    {
        $links = [
            'Site réalisé par Infelix Commentator' => Router::generateUrl('app_InfelixCommentator'),
        ];
        return array_merge($links, $this->getLuckyLink());
    }

    protected function getTitle(): string
    {
        return $this->title;
    }

    protected function setLangs(): array
    {
        return [
            
        ];
    }

    protected function trans(string $value, ?string $domain = null): string
    {
        if ($domain === null) {
            $domain = $this->name;
        }
        return Lang::trans($value, $domain);
    }

    protected function renderTemplate($variables = []): void
    {
        // $this->addJS('copy');

        $siteRoot = $_ENV["PROJECT_ROOT"];
        $templatesRoot = BASE_PATH.'/templates/';
        $stylesheets = $this->stylesheets;
        $scripts = $this->scripts;
        $title = $this->title;

        if ($siteRoot[0] !== '/') {
            $siteRoot = '/' . $siteRoot;
        }

        $templateLink = $templatesRoot . $this->name . '/' . $this->template;
        if ($this->name == null || $this->template == null) {
            $templateLink = $templatesRoot . '/index/index.php';
        }

        $menuLinks = $this->setMenuLinks();
        $langs = $this->setLangs();
        $footerLinks = $this->setFooterLinks();

        $indexLink = Router::generateUrl('app_index');

        $siteName = $this->getSiteName();

        $content = file_get_contents($templateLink);

        $logo = $this->getFavicon();

        $socials = $this->getSocials();

        $bodyClass = $this->getBodyClass();

        foreach($variables as $varName => $varValue){
            $$varName = $varValue;
        }

        require_once $templatesRoot . $this->getBaseTemplate();
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }

    protected function getBodyClass(): string
    {
        return str_replace('/', '-', $this->name);
    }

    protected function getSocials(): array
    {
        return [
            'social_discord' => [
                'link' => Configuration::get('social_discord'),
                'icon' => AssetsService::getImageContent('picto-socials/discord', 'svg'), // Router::generateUrl('app_media_image',['path'=> 'picto-socials/discord', 'extension' => 'svg']),
                'title' => 'Discord', 
                'type' => 'url',
                'iconExtension' => 'svg',
            ],
            'social_gamejolt' => [
                'link' => Configuration::get('social_gamejolt'),
                'icon' => AssetsService::getImageContent('picto-socials/gamejolt', 'svg'), // Router::generateUrl('app_media_image',['path'=> 'picto-socials/gamejolt', 'extension' => 'svg']),
                'title' => 'Gamejolt',
                'type' => 'url',
                'iconExtension' => 'svg',
            ],
            'social_twitch' => [
                'link' => Configuration::get('social_twitch'),
                'icon' => AssetsService::getImageContent('picto-socials/twitch', 'svg'), // Router::generateUrl('app_media_image',['path'=> 'picto-socials/twitch', 'extension' => 'svg']),
                'title' => 'Twitch',
                'type' => 'url',
                'iconExtension' => 'svg',
            ],
            'social_youtube' => [
                'link' => Configuration::get('social_youtube'),
                'icon' => AssetsService::getImageContent('picto-socials/youtube', 'svg'), // Router::generateUrl('app_media_image',['path'=> 'picto-socials/youtube', 'extension' => 'svg']),
                'title' => 'Youtube',
                'type' => 'url',
                'iconExtension' => 'svg',
            ],
        ];
    }
    protected function getBaseTemplate(): string
    {
        return 'base.php';
    }

    public function __construct()
    {
    }
}