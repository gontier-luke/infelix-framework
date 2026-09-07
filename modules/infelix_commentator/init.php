<?php

$moduleRoot = __DIR__ . '/';

foreach(glob($moduleRoot . 'enum/*.php') as $fileName){
    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob($moduleRoot . 'models/*.php') as $fileName){
    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob($moduleRoot . 'repositories/*.php') as $fileName){
    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob($moduleRoot . 'services/*.php') as $fileName){
    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob($moduleRoot . 'controllers/*.php') as $fileName){
    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}