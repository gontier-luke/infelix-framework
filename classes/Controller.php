<?php

class ControllerCore{
    protected string $name;
    protected string $template;
    protected array $stylesheets = [];
    protected array $scripts = [];
    protected string $title = 'Infelix Commentator - base';

    public static function getInstanceByName($name) : ControllerCore {
        $controllerPath = BASE_PATH . '/controllers/' . ucfirst($name) . 'Controller.php';
        if (!file_exists($controllerPath)) {

            $controller = new NotFoundController();
            $controller->setName('notFound');
            return $controller;
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
        $this->scripts[] = $script;
    }

    protected function setTitle($title): void
    {
        $this->title = $title;
    }

    protected function renderTemplate($variables = []): void
    {
        $this->addCSS('popupCaptchaStyle.css');
        $this->addCSS('base.css');
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

        $content = file_get_contents($templateLink);

        require_once $templatesRoot . 'base.php';
    }
}