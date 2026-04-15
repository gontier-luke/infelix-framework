<?php

namespace Classes;

require_once BASE_PATH . 'classes/Controller.php';

class AdminControllerCore extends ControllerCore{
    
    public function __construct()
    {
        $this->addCSS('admin/style');
        $this->addJS('admin/script');
    }

}