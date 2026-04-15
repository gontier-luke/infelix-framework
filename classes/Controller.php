<?php
namespace Classes;

use Repositories\Configuration;
use Enum\LangEnum as Lang;

class ControllerCore{
    protected string $name;
    protected string $template;
    protected array $stylesheets = [];
    protected array $scripts = [];
    protected string $title = 'Infelix Commentator - base';

    public static function getInstanceByName(string $name) : ?ControllerCore {
        $controllerPath = BASE_PATH . '/controllers/' . ucfirst($name) . 'Controller.php';
        if (!file_exists($controllerPath)) {
            return null;
        }
        $className =  ucfirst($name) .'Controller';
        require_once BASE_PATH . '/controllers/' . $className .'.php';
        $className = '\Controllers\\'.$className;
        
        /** @var ControllerCore */  
        $controller = new $className();
        $controller->setName($name);
        $controller->addCSS('style');
        $controller->addJS('https://code.jquery.com/jquery-3.7.1.min.js');
        return $controller;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    protected function addCSS($stylesheet): void
    {
        $this->stylesheets[] =  Router::generateUrl('app_media_css', ['path' => $stylesheet]);
    }

    protected function addJS($script): void
    {
        if (str_starts_with($script, 'http')) {
            $this->scripts[] = $script;
            return;
        }
        $this->scripts[] = $_ENV["PROJECT_ROOT"] . 'script/'.$script.'.js';
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
            'Présentation' => Router::generateUrl(name: 'app_presentation'), // Présentation de HAM ainsi que de l'équipe
            'Jeux & Histoires' => Router::generateUrl('app_jeux_histoires'), // Présentation des jeux et des histoires (projets)
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

    protected function trans(string $value, string $domain = 'base'): string
    {
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
        ];
    }
    protected function getBaseTemplate(): string
    {
        return 'base.php';
    }
}