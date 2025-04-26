<?php

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
        $controller = new $className();
        $controller->setName($name);
        return $controller;
    }
    
    public function run(): bool
    {
        return true;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    protected function addCSS($stylesheet): void
    {
        $this->stylesheets[] =  $_ENV["PROJECT_ROOT"] . 'build/css/' . $stylesheet;
    }

    protected function addJS($script): void
    {
        if (str_contains($script, 'http')) {
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

    protected function getFavicon(): string
    {
        return   $_ENV["PROJECT_ROOT"] . 'build/img/' . Configuration::get('logo');
    }
    
    /**
     * @return array<string, string>
     */
    protected function setMenuLinks(): array
    {
        return [
            'Curriculum Vitæ' => Router::generateUrl('app_cv'),
            'Darkest Dungeon JDRPG' => Router::generateUrl('app_DarkestDungeon'),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function getLuckyLink(): array
    {
        $luckyLink = [];

        if (rand(0, 854) === 42) {
            $luckyLink['Lucky link'] = Router::generateUrl('app_lucky');
        }

        return $luckyLink;
    }

    /**
     * @return array<string, string>
     */
    protected function setFooterLinks(): array
    {
        $links = [
            'CV' => Router::generateUrl('app_cv'),
            'Darkest Dungeon JDRPG' => Router::generateUrl('app_DarkestDungeon'),
        ];
        return array_merge($links, $this->getLuckyLink());
    }

    protected function getTitle(): string
    {
        return $this->title;
    }

    protected function renderTemplate($variables = []): void
    {
        $this->addCSS('popupCaptchaStyle.css');
        $this->addCSS('base.css');
        $this->addJS('https://code.jquery.com/jquery-3.7.1.min.js');
        $this->addJS('copy');
        $this->addJS('slider');

        $siteRoot = $_ENV["PROJECT_ROOT"];
        $templatesRoot = BASE_PATH.'/templates/';
        $stylesheets = $this->stylesheets;
        $scripts = $this->scripts;
        $title = $this->title;

        if ($siteRoot[0] !== '/') {
            $siteRoot = '/' . $siteRoot;
        }
        foreach($variables as $varName => $varValue){
            $$varName = $varValue;
        }

        $templateLink = $templatesRoot . $this->name . '/' . $this->template;
        if ($this->name == null || $this->template == null) {
            $templateLink = $templatesRoot . '/index/index.php';
        }

        $menuLinks = $this->setMenuLinks();
        $footerLinks = $this->setFooterLinks();

        $indexLink = Router::generateUrl('app_index');

        $siteName = $this->getSiteName();

        $content = file_get_contents($templateLink);

        $logo = $this->getFavicon();

        $socials = $this->getSocials();

        require_once $templatesRoot . 'base.php';
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit();
    }

    protected function getSocials(): array
    {
        return [
            'social_instagram' => [
                'link' => Configuration::get('social_instagram'),
                'icon' => 'image/picto-socials/instagram.svg',
                'title' => 'Instagram',
                'type' => 'url',
            ],
            'social_linkedin' => [
                'link' => Configuration::get('social_linkedin'),
                'icon' => 'image/picto-socials/linkedin.svg',
                'title' => 'LinkedIn',
                'type' => 'url',
            ],
            'social_discord' => [
                'link' => Configuration::get('social_discord'),
                'icon' => 'image/picto-socials/discord.svg',
                'title' => 'Discord', 
                'type' => 'copy',
                'copy_message' => '<p>Mon identifiant Discord a été copié dans votre presse-papier.</p><p>Vous pouvez maintenant me contacter sur Discord après une demande d\'ajout d\'ami.</p><p> Si l\'identifiant n\'a pas été copié, vous pouvez le copier manuellement ici :</p>' . Configuration::get('social_discord'),
            ],
            'social_github' => [
                'link' => Configuration::get('social_github'),
                'icon' => 'image/picto-socials/github.svg',
                'title' => 'GitHub', 
                'type' => 'url',
            ],
            
        ];
    }

    protected function generateHTMLLink(string $link, string $label, string $class = '', string $target = '', string $title = ''): string
    {
        $target = $target ? ' target="' . $target . '"' : '';
        $class = $class ? ' class="' . $class . '"' : '';
        return '<a href="' . $link . '"' . $target . $class . ' title="' . $title . '">' . $label . '</a>';
    }

}