<?php 

session_start();

foreach(glob('../classes/*.php') as $fileName){

    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob('../modeles/*.php') as $fileName){

    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}
foreach(glob('../controllers/*.php') as $fileName){

    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}
foreach(glob('../enum/*.php') as $fileName){

    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    die;
}

$controller = AdminControllerCore::getInstanceByName(basename(__DIR__));
$controller->run();