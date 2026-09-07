<?php

namespace Controllers;

use Classes\AdminControllerCore;
use Classes\Router;
use Services\AdminService;
use Services\AdminModulesService;

class AdminModuleController extends AdminControllerCore
{
    # [Route('/admin/liste-modules', 'app_admin_modules')]
    public function modules(): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        $this->template = 'modulesList.php';
        $this->setTitle('Liste des Modules');
        $this->renderTemplate(['modules' => AdminModulesService::getModulesForDisplay(), 'flashMessage' => $_SESSION['flash_message'] ?? null] );
        unset($_SESSION['flash_message']);
        return true;
    }

    # [Route('/admin/module/installer/{id}', 'app_admin_module_install')]
    public function installModule(int $id): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }

        $module = AdminModulesService::getModuleById($id);
        if(!$module) {
            Router::redirect('app_admin_modules');
            return true;
        }

        try {
            AdminModulesService::installModule($module->getName());
            $_SESSION['flash_message']['message'] = "Le module '{$module->getName()}' a été installé avec succès.";
            $_SESSION['flash_message']['color'] = "bg-green-500";
        } catch (\Exception $e) {
            $_SESSION['flash_message']['message'] = "Erreur lors de l'installation du module '{$module->getName()}': " . $e->getMessage();
            $_SESSION['flash_message']['color'] = "bg-red-500";
        }

        Router::redirect('app_admin_modules');
        return true;
    }

    # [Route('/admin/module/désinstaller/{id}', 'app_admin_module_uninstall')]
    public function uninstallModule(int $id): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }

        $module = AdminModulesService::getModuleById($id);
        if(!$module) {
            Router::redirect('app_admin_modules');
            return true;
        }

        try {
            AdminModulesService::uninstallModule($module->getName());
            $_SESSION['flash_message']['message'] = "Le module '{$module->getName()}' a été désinstallé avec succès.";
            $_SESSION['flash_message']['color'] = "bg-green-500";
        } catch (\Exception $e) {
            $_SESSION['flash_message']['message'] = "Erreur lors de la désinstallation du module '{$module->getName()}': " . $e->getMessage();
            $_SESSION['flash_message']['color'] = "bg-red-500";
        }

        Router::redirect('app_admin_modules');
        return true;
    }
}
        