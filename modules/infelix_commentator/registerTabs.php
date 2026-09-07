
<?php
use Classes\Router;
use Services\AdminService;

AdminService::addAdminMenuLink('Projets', null,'projetsInfelix');
AdminService::addAdminMenuLink('Sites', Router::generateUrl('app_admin_sites'),'sites','projetsInfelix');
AdminService::addAdminMenuLink('Jeux', Router::generateUrl('app_admin_jeux'),'jeux','projetsInfelix');
AdminService::addAdminMenuLink('Page d\'accueil', Router::generateUrl('app_admin_accueil'),'accueil');
AdminService::addAdminMenuLink('CV', Router::generateUrl('app_admin_cv'),'cv');