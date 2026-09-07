<?php

namespace Override;

use Classes\ControllerCore;
use Classes\Router;
use Repositories\Configuration;
use Services\AssetsService;

/**
 * Override de la classe ControllerCore pour les cas spécifiques au site
 */
class ControllerOverride extends ControllerCore
{

    protected function getSiteName(): string
    {
        return 'Infelix Commentator';
    }

    protected function setMenuLinks(): array
    {
        return [
            'Curriculum Vitæ' => Router::generateUrl('app_cv'),
            'Darkest Dungeon JDRPG' => Router::generateUrl('app_DarkestDungeon'),
        ];
    }

    protected function getSocials(): array
    {
        return [
            'social_instagram' => [
                'link' => Configuration::get('social_instagram'),
                'icon' => AssetsService::getImageContent('picto-socials/instagram', 'svg'),
                'title' => 'Instagram',
                'type' => 'url',
                'iconExtension' => 'svg',
            ],
            'social_linkedin' => [
                'link' => Configuration::get('social_linkedin'),
                'icon' => AssetsService::getImageContent('picto-socials/linkedin', 'svg'),
                'title' => 'LinkedIn',
                'type' => 'url',
                'iconExtension' => 'svg',
            ],
            'social_discord' => [
                'link' => Configuration::get('social_discord'),
                'icon' => AssetsService::getImageContent('picto-socials/discord', 'svg'),
                'title' => 'Discord', 
                'type' => 'copy',
                'iconExtension' => 'svg',
                'copy_message' => '<p>Mon identifiant Discord a été copié dans votre presse-papier.</p><p>Vous pouvez maintenant me contacter sur Discord après une demande d\'ajout d\'ami.</p><p> Si l\'identifiant n\'a pas été copié, vous pouvez le copier manuellement ici :</p>' . Configuration::get('social_discord'),
            ],
            'social_github' => [
                'link' => Configuration::get('social_github'),
                'icon' => AssetsService::getImageContent('picto-socials/github', 'svg'),
                'title' => 'GitHub', 
                'type' => 'url',
                'iconExtension' => 'svg',
            ],
            

        ];
    }
}
