<?php 

$basePath = explode('admin34572ed7dqk',dirname(__FILE__))[0];
$basePath = str_replace('\\','/',$basePath);
define('BASE_PATH',$basePath);
define('ADMIN_PATH',$basePath.'/admin34572ed7dqk/');

if (!file_exists(BASE_PATH.'/.env')) {
    throw new Exception("Le fichier .env n'existe pas.");
}

$file = file(BASE_PATH.'/.env');
foreach ($file as $line) {
  if (strpos(trim($line), '#') === 0) {
    continue;
  }

  list($key, $value) = explode(':', $line, 2);
  $key = trim($key);
  $value = trim($value);

  if (!array_key_exists($key, $_ENV)) {
    putenv(sprintf('%s=%s', $key, $value));
    $_ENV[$key] = $value;
  }
}

class AdminControllerCore{
    protected string $name;
    protected string $template;

    public static function getInstanceByName($name) : AdminControllerCore {
        $className =  ucfirst($name) .'AdminController';
        require_once ADMIN_PATH.'/controllers/' . $className .'.php';
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

    protected function renderTemplate($variables = []): void
    {
        foreach($variables as $varName => $varValue){
            $$varName = $varValue;
        }
        $adminRoot = '/admin34572ed7dqk/';

        if ($_ENV['PROJECT_ROOT'] != '/') {
            $adminRoot = $_ENV['PROJECT_ROOT'].'/'.$adminRoot;
        }
        $templatesRoot = ADMIN_PATH.'/templates/';
        if ($adminRoot[0] !== '/') {
            $adminRoot = '/' . $adminRoot;
        }

        $siteRoot = $_ENV["PROJECT_ROOT"];
        $templatesSiteRoot = BASE_PATH.'/templates/';
        if ($siteRoot[0] !== '/') {
            $siteRoot = '/' . $siteRoot;
        }
        require ADMIN_PATH.'/templates/' . $this->name . '/' . $this->template;
    }
}