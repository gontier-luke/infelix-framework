<?php

namespace Controllers;

use Classes\AdminControllerCore;
use Classes\Router;
use Services\AdminService;
use Services\AdminUsersService;

class AdminUsersCRUDController extends AdminControllerCore
{
    # [Route('/admin/utilisateurs-admin', 'app_admin_admin_users')]
    public function index(): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        $this->template = 'usersList.php';
        $this->setTitle('Utilisateurs Administrateurs');
        $this->renderTemplate(['adminUsers' => AdminUsersService::getAdminUsersForDisplay(), 'createAdminUserButton' => AdminUsersService::getCreateAdminUserButton()] );
        return true;
    }

    # [Route('/admin/utilisateurs-admin/update/{id}', 'app_admin_admin_users_update')]
    public function update(int $id): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        if($_POST) {
            if(AdminUsersService::updateAdminUser($id, $_POST)) {
                Router::redirect('app_admin_admin_users');
                return true;
            }
            return false;
        }
        $this->template = 'userEdit.php';
        $this->setTitle('Modifier Utilisateur Administrateur');
        $userForm = AdminUsersService::getAdminUserForm($id);
        $this->renderTemplate(['action' => 'update', 'id' => $id, 'form' => $userForm, 'titreForm' => $this->trans('Modifier un utilisateur administrateur')]);
        return true;
    }

    # [Route('/admin/utilisateurs-admin/create', 'app_admin_admin_users_create')]
    public function create(): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        if($_POST && AdminUsersService::createAdminUser($_POST)) {
            Router::redirect('app_admin_admin_users');
            return true;
        }
        $this->template = 'userEdit.php';
        $this->setTitle('Créer Utilisateur Administrateur');
        $this->renderTemplate(['action' => 'create', 'form' => AdminUsersService::getAdminUserForm(), 'titreForm' => $this->trans('Créer un utilisateur administrateur')]);
        return true;
    }

    # [Route('/admin/utilisateurs-admin/delete/{id}', 'app_admin_admin_users_delete')]
    public function delete(int $id): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        $this->template = 'userEdit.php';
        $this->setTitle('Supprimer Utilisateur Administrateur');
        AdminUsersService::deleteAdminUser($id);
        Router::redirect('app_admin_admin_users');
        return true;
    }

    # [Route('/admin/utilisateurs-admin/reset-password/{id}', 'app_admin_admin_users_reset_password_id')]
    public function resetPasswordById(int $id): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        $this->template = 'userResetPassword.php';
        $this->setTitle('Réinitialiser le mot de passe de l\'utilisateur administrateur');
        $this->renderTemplate(['id' => $id]);
        return true;
    }

}
        