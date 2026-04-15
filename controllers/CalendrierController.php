<?php

namespace Controllers;

use Classes\FormBuilder;
use Override\ControllerOverride;
use Classes\Router;
use Services\CalendrierService;
use Enum\CalendrierGameEnum;
use Enum\InputTypeEnum;

class CalendrierController extends ControllerOverride
{
    protected string $name;

    public function __construct()
    {
        $this->template = 'index.php';
        $this->setTitle('Accueil');
    }

    # [Route('/calendrier', 'app_calendrier')]
    public function calendrier(): bool
    {
        if(!CalendrierService::isUserLogged($_SESSION)) {
            Router::redirect('app_calendrier_connexion');
            return false;
        }
        $this->renderTemplate([
            'cases' => [],
            'deconnexion_url' => Router::generateUrl('app_calendrier_deconnexion'),
        ]);
        return true;
    }

    # [Route('/pioupiou', 'app_pioupiou')]
    public function pioupiou(): bool
    {
        if(!CalendrierService::verifyGameAccess(CalendrierGameEnum::PIOUPIOU)) {
            Router::redirect('app_calendrier');
            return false;
        }

        $this->template = 'pioupiou.php';
        $this->addJS('pioupiou');
        $this->renderTemplate([
            'pioupiou_up' => Router::generateUrl('app_media_image', ['path' => 'pioupiou/pioupiou_up', 'extension' => 'png']),
            'pioupiou_down' => Router::generateUrl('app_media_image', ['path' => 'pioupiou/pioupiou_down', 'extension' => 'png']),
            'pioupiou_pipe' => Router::generateUrl('app_media_image', ['path' => 'pioupiou/pioupiou_pipe', 'extension' => 'png']),
            'pioupiou_bg' => Router::generateUrl('app_media_image', ['path' => 'pioupiou/pioupiou_bg', 'extension' => 'png']),
        ]);
        return true;
    }

    # [Route('/calendrier/connexion', 'app_calendrier_connexion')]
    public function connexion(): bool
    {
        $this->template = 'connexion.php';
        $error = null;
        $form = (new FormBuilder())
            ->setMethod('POST')
            ->add('username', 'Nom d\'utilisateur', InputTypeEnum::TEXT, true)
            ->add('password', 'Mot de passe', InputTypeEnum::PASSWORD, true)
            ->add('submit', 'Se connecter', InputTypeEnum::SUBMIT, true);
        if($_POST) {
            if(array_key_exists('submit', $_POST) && array_key_exists('username', $_POST) && array_key_exists('password', $_POST)) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                if(CalendrierService::login($username, $password)) {
                    Router::redirect('app_calendrier');
                    return true;
                }
                $error = "Nom d'utilisateur ou mot de passe incorrect.";
            }
        }
        $this->renderTemplate([
            'form' => $form->renderForm(),
            'error' => $error,
        ]);
        return true;
    }

    # [Route('/calendrier/deconnexion', 'app_calendrier_deconnexion')]
    public function deconnexion(): bool
    {
        CalendrierService::logout();
        Router::redirect('app_calendrier');
        return true;
    }

    # [Route('/calendrier/histoire-hero', 'app_calendrier_histoire_hero')]
    public function histoireHero(): bool{
        if(!CalendrierService::verifyGameAccess(CalendrierGameEnum::HISTOIREHERO)) {
            Router::redirect('app_calendrier');
            return false;
        }
        $this->template = 'histoire_hero.php';
        $this->renderTemplate([]);
        return true;
    }

    # [Route('/calendrier/dessine-avec-moi', 'app_calendrier_dessine_avec_moi')]
    public function dessineAvecMoi(): bool{
        if(!CalendrierService::verifyGameAccess(CalendrierGameEnum::DESSINS)) {
            Router::redirect('app_calendrier');
            return false;
        }
        $this->template = 'histoire_hero.php';
        $this->renderTemplate([]);
        return true;
    }

    # [Route('/calendrier/dessine-avec-moi', 'app_calendrier_dessine_avec_moi')]
    public function dessineAvecMoi(): bool{
        if(!CalendrierService::verifyGameAccess(CalendrierGameEnum::DESSINS)) {
            Router::redirect('app_calendrier');
            return false;
        }
        $this->template = 'histoire_hero.php';
        $this->renderTemplate([]);
        return true;
    }
}