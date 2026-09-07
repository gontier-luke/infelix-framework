<?php

namespace Services;

use Classes\FormBuilder;
use Classes\Router;
use Classes\Route;
use Enum\InputTypeEnum;
use Enum\LangEnum as Lang;
use Models\PageModel;
use Repositories\PageRepository;
/**
 * Service pour la gestion de l'interface d'administration des pages
 */
class PagesService
{
    /**
     * Récupérer la liste des pages
     * @return array<PageModel> La liste des pages
     */
    private static function getPagesList(): array
    {
        $pageRepo = new PageRepository();
        return $pageRepo->getAllPages();
    }

    /**
     * Formater les données pour afficher la liste des pages
     * @return array Les données formatées pour l'affichage
     */
    public static function getPagesForDisplay(): array
    {
        $pages = self::getPagesList();
        $formattedPages = [];
        foreach ($pages as $page) {
            $formattedPages[] = [
                'app_name' => $page->getAppName(),
                'name' => $page->getName(),
                'active' => $page->isActive(),
                'actions' => AdminService::getCRUDEditActions('pages', $page->getId()),
            ];
        }
        return $formattedPages;
    }

    /**
     * Récupérer le bouton pour créer un nouvel utilisateur administrateur
     * @return string Le code HTML du bouton
     */
    public static function getCreatePageButton(): string
    {
        return createHTMLABalise(
            'createPage',
            Lang::trans('Créer une page', 'admin'),
            Router::generateUrl('app_admin_pages_create'),
            'btn'
        );
    }

    /**
     * Générer le formulaire d'édition d'un utilisateur administrateur
     * @param ?int $id L'ID de l'utilisateur administrateur
     * @return string Le code HTML du formulaire
     */
    public static function getPageForm(?int $id = null): string
    {
        $redirectAppName = 'app_admin_pages_create';
        $redirectParams = [];
        if($id) {
            $redirectAppName = 'app_admin_pages_update';
            $redirectParams = ['id' => $id];
        } 
        $page = new PageModel($id);
        $formBuilder = (new FormBuilder())
            ->add('appName', Lang::trans('Page à sélectionner', 'admin'), InputTypeEnum::SELECT, true, $page->getAppName(),[
                'options' => self::getPageSelection(),
            ])
            ->setFromModel($page)
            ->removeInput('app_name')
            ->editInput('active', defaultValue: 'active', required: false)
            ->add('submit', Lang::trans('Enregistrer', 'admin'), InputTypeEnum::SUBMIT)
            ->setAction(Router::generateUrl($redirectAppName, $redirectParams))
            ->setClass('crud-form')
            ;
            if($id) {
                if(!$page->isActive()) {
                    $formBuilder->editInput('active', defaultValue: '');
                } 
            }
        return $formBuilder->renderForm();
    }

    /**
     * Mettre à jour un utilisateur administrateur
     * @param int $id L'ID de l'utilisateur à mettre à jour
     * @param array $data Les données à mettre à jour
     * @return bool Indique si la mise à jour a réussi
     */
    public static function updatePage(int $id, array $data): bool
    {
        $page = new PageModel($id);
        $page->setAppName($data['appName']);
        $page->setName($data['name']);
        $page->setActive(isset($data['active']) ? true : false);
        return $page->update();
    }

    /**
     * Créer un nouvel utilisateur administrateur
     * @param array $data Les données du nouvel utilisateur
     * @return bool Indique si la création a réussi
     */
    public static function createPage(array $data): bool
    {
        $page = new PageModel();
        $page->setAppName($data['appName']);
        $page->setName($data['name']);
        $page->setActive(isset($data['active']) ? true : false);
        $result = $page->insert();

        return $result;
    }

    /**
     * Obtenir la liste des pages disponibles
     * @return array<PageModel> La liste des pages
     */
    public static function getPageSelection(): array
    {
        /** @var Route[] */
        $pages = Router::$routes->filter(function($route) {
            return $route->isAdminRoute === false;
        })->getAll();
        
        $pagesFormatted = [];
        foreach($pages as $page) {
            $pagesFormatted[$page->getAppName()] = $page->getAppName() . ' - ' . $page->getPath();
        }

        return $pagesFormatted;
    }

    public static function deletePage(int $id): bool
    {
        $page = new PageModel($id);
        return $page->delete();
    }
}