<?php

namespace Controllers;

use Classes\AdminControllerCore;
use Classes\Router;
use Services\AdminService;
use Services\PagesService;

class PagesCRUDController extends AdminControllerCore
{
    # [Route('/admin/pages', 'app_admin_pages')]
    public function index(): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        $this->template = 'pagesList.php';
        $this->setTitle('Pages');
        $this->renderTemplate(['pages' => PagesService::getPagesForDisplay(), 'createPageButton' => PagesService::getCreatePageButton()] );
        return true;
    }

    # [Route('/admin/pages/update/{id}', 'app_admin_pages_update')]
    public function update(int $id): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        if($_POST) {
            if(PagesService::updatePage($id, $_POST)) {
                Router::redirect('app_admin_pages');
                return true;
            }
            return false;
        }
        $this->template = 'pageEdit.php';
        $this->setTitle('Modifier Page');
        $pageForm = PagesService::getPageForm($id);
        $this->renderTemplate(['action' => 'update', 'id' => $id, 'form' => $pageForm, 'titreForm' => $this->trans('Modifier une page')]);
        return true;
    }

    # [Route('/admin/pages/create', 'app_admin_pages_create')]
    public function create(): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        if($_POST && PagesService::createPage($_POST)) {
            Router::redirect('app_admin_pages');
            return true;
        }
        $this->template = 'pageEdit.php';
        $this->setTitle('Créer Page');
        $this->renderTemplate(['action' => 'create', 'form' => PagesService::getPageForm(), 'titreForm' => $this->trans('Créer une page')]);
        return true;
    }

    # [Route('/admin/pages/delete/{id}', 'app_admin_pages_delete')]
    public function delete(int $id): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        $this->template = 'pageEdit.php';
        $this->setTitle('Supprimer Page');
        PagesService::deletePage($id);
        Router::redirect('app_admin_pages');
        return true;
    }

}
        