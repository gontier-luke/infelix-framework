<?php

namespace Controllers;

use Classes\AdminControllerCore;
use Classes\Router;
use Services\AdminService;

class AdminDashBoardController extends AdminControllerCore
{
    # [Route('/admin/', 'app_admin_dashboard')]
    public function dashboard(): bool
    {
        if(!AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_login');
            return true;
        }
        $this->template = 'dashboard.php';
        $this->setTitle('Admin Dashboard');
        $this->renderTemplate();
        return true;
    }
}
