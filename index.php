<?php 

session_start();

require_once 'var/base_func.php';


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
if(Configuration::get('ssl') === '1') {
    if($_SERVER['REQUEST_SCHEME'] === 'http') {
        header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        exit;
    }
}
$router = new Router();
$router->handleRequest($_GET['path'] ?? '/');
