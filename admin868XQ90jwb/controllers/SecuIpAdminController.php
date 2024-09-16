<?php

require_once BASE_PATH . '/modeles/SecuIpModel.php';

class SecuIpAdminController extends AdminControllerCore
{
    private array $errors = [];

    public function __construct()
    {
        $this->template = 'index.php';
    }

    public function run(): bool
    {
        try {
            if (!key_exists('token', $_SESSION)){
                header('Location: ../');
                die;
            }
                
            if( $_SESSION['token'] != (new LoginAdminController())->getToken($_SESSION['login'])) {
                header('Location: ../');
                die;
            }
            
            if (key_exists('action', $_POST)) {
                $this->executeAction($_POST);
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        
        if ($this->errors != []) {
            $_SESSION['ipErrors'] = json_encode($this->errors);
        }
        
        header('Location: ../');
        return true;
    }

    private function executeAction($params)
    {
        switch ($params['action']) {
            case 'desactivate':
                (new SecuIpModel())->addDesacIp('all', $params['tempo']);
                break;
        }
    }
}
