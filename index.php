<?php 

use classes\Router;
use Repositories\Configuration;

session_start();

require_once 'var/base_func.php';

foreach(glob('./enum/*.php') as $fileName){

    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob('./classes/*.php') as $fileName){
    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob('./classes/**/*.php') as $fileName){
    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob('./overrides/*.php') as $fileName){
    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob('./controllers/*.php') as $fileName){
    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}


foreach(glob('./models/*.php') as $fileName){

    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob('./repositories/*.php') as $fileName){

    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob('./services/*.php') as $fileName){

    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

$activeModules = getAllActiveModules();

foreach($activeModules as $module) {
    $modulePath = './modules/' . $module->getName() . '/init.php';
    if(file_exists($modulePath)) {
        require_once $modulePath;
    }
}


if(Configuration::get('ssl') === '1') {
    if($_SERVER['REQUEST_SCHEME'] === 'http') {
        header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        exit;
    }
}
$router = new Router();
try {
    $router->handleRequest($_GET['path'] ?? '/');
} catch (Exception $e) {
    dd($e->getMessage());
}
