<?php


$settings = parseXmlFile(BASE_PATH . 'modules/infelix_commentator/settings.xml');
$initPath = DIRNAME(__FILE__) . '/init.php';
if(file_exists($initPath)) {
    require_once $initPath;
}   

checkTable(
    'calendrier_user',
    'infelix_commentator'
);
checkTable(
    'formation',
    'infelix_commentator'
);

