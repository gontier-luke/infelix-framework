<?php

namespace Controllers;

use Classes\FormBuilder;
use Override\ControllerOverride;
use Classes\Router;
use Services\CalendrierService;
use Enum\CalendrierGameEnum;
use Enum\InputTypeEnum;
use Modules\PokemonFight\Enums\TrainerEnum;

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

    # [Route('/calendrier/histoire-hero/debloquer-chapitre/{chapter}', 'app_calendrier_histoire_hero_debloquer_chapitre')]
    public function histoireHeroDebloChap(int $chapter): bool{
        if(!CalendrierService::verifyGameAccess(CalendrierGameEnum::HISTOIREHERO)) {
            Router::redirect('app_calendrier');
            return false;
        }
        if(CalendrierService::unlockHistoireHeroChapter($chapter)) {
            Router::redirect('app_calendrier_histoire_hero');
        }
        return true;
    }

    # [Route('/calendrier/dessine-avec-moi', 'app_calendrier_dessine_avec_moi')]
    public function dessineAvecMoi(): bool{
        if(!CalendrierService::verifyGameAccess(CalendrierGameEnum::DESSINS)) {
            Router::redirect('app_calendrier');
            return false;
        }
        $this->template = 'dessineAvecMoi.php';
        $this->renderTemplate(['model' => CalendrierService::getARandomModelToDraw((int) (new \DateTime())->format('d'))]);
        return true;
    }

    # [Route('/calendrier/pioupiou', 'app_calendrier_pioupiou')]
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

    # [Route('/calendrier/karaoke', 'app_calendrier_karaoke')]
    public function karaoke(): bool
    {
        if(!CalendrierService::verifyGameAccess(CalendrierGameEnum::KARAOKE)) {
            Router::redirect('app_calendrier');
            return false;
        }
        $karaokeData = CalendrierService::getKaraokeVideoAndAudioRandom((int) (new \DateTime())->format('d'));
        $karaoke_video = $karaokeData['video'];
        $karaoke_audio = $karaokeData['audio'];
        $this->template = 'karaoke.php';
        $this->addJS('karaoke');
        $this->renderTemplate([
            'karaoke_video' => $karaoke_video,
            'karaoke_luke' => $karaoke_audio,
            'mute' => Router::generateUrl('app_media_image', ['path' => 'karaoke/mute', 'extension' => 'png']),
            'unmute' => Router::generateUrl('app_media_image', ['path' => 'karaoke/unmute', 'extension' => 'png']),
            'pause' => Router::generateUrl('app_media_image', ['path' => 'karaoke/pause', 'extension' => 'png']),
        ]);
        return true;
    }

    # [Route('/calendrier/pokemon', 'app_calendrier_pokemon')]
    public function pokemon(): bool
    {
        dd(TrainerEnum::setUp(TrainerEnum::ZARFIGNUM)->_toString());
        if(!CalendrierService::verifyGameAccess(CalendrierGameEnum::POKEMON)) {
            Router::redirect('app_calendrier');
            return false;
        }

        $this->template = 'pokemon.php';
        $this->addJS('pokemon');
        $this->renderTemplate([
        ]);
        return true;
    }

    # [Route('/calendrier/luklicker', 'app_calendrier_luklicker')]
    public function luklicker(): bool
    {
        if(!CalendrierService::verifyGameAccess(CalendrierGameEnum::CLICKER)) {
            Router::redirect('app_calendrier');
            return false;
        }

        $this->template = 'luklicker.php';
        $this->addJS('luklicker');
        $this->renderTemplate([
        ]);
        return true;
    }
}