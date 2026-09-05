<?php

$enums = glob(BASE_PATH . 'modules/pokemonfight/enums/*.php');
foreach ($enums as $enum) {
    require_once $enum;
}
$entities = glob(BASE_PATH . 'modules/pokemonfight/entities/*.php');
foreach ($entities as $entity) {
    require_once $entity;
}
$models = glob(BASE_PATH . 'modules/pokemonfight/models/*.php');
foreach ($models as $model) {
    require_once $model;
}
$services = glob(BASE_PATH . 'modules/pokemonfight/services/*.php');
foreach ($services as $service) {
    require_once $service;
}

$controllers = glob(BASE_PATH . 'modules/pokemonfight/controllers/*.php');
foreach ($controllers as $controller) {
    require_once $controller;
}