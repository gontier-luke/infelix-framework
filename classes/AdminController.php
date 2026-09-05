<?php

namespace Classes;
use Services\AdminService;

require_once BASE_PATH . 'classes/Controller.php';

class AdminControllerCore extends ControllerCore{
    
    public function __construct()
    {
        $this->addCSS('admin/style');
        // $this->addJS('admin/script');
    }

    protected function getBaseTemplate(): string
    {
        return 'admin/base.php';
    }

    protected function getBodyClass(): string
    {
        return 'admin '.parent::getBodyClass();
    }
    
    protected function getFavicon(): string
    {
        // return Router::generateUrl('app_media_image', ['path' => 'logo-' . SaisonsEnum::INDETERMINE->value, 'extension' => 'png']);
        return Router::generateUrl('app_media_image', ['path' => 'logo-neutre', 'extension' => 'png']);
    }

    protected function setMenuLinks(): array
    {
        $modules = getAllActiveModules();
        foreach($modules as $module) {
            $moduleName = $module->getName();
            $registerTabsFile = BASE_PATH . 'modules/' . $moduleName . '/registerTabs.php';
            if(file_exists($registerTabsFile)) {
                require_once $registerTabsFile;
            }
        }
        return AdminService::getAdminMenuLinks();
    }
}