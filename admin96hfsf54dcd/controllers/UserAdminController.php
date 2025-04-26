<?php 

class UserAdminController extends AdminControllerCore{
    private array $errors = [];

    public function __construct()
    {
        $this->template = 'index.php';
    }

    public function run(): bool
    {
        try {
            if(!key_exists('token', $_SESSION)){

                header('Location: ../');
                die;
            }

            if(!((new UserModel())->isOwner($_SESSION['login'])) || $_SESSION['token'] != (new LoginAdminController())->getToken($_SESSION['login'])){
                header('Location: ../');
                die;
            }

            if(key_exists('action', $_GET)){
                $this->executeAction($_GET);
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        if ($this->errors != []) {
            $_SESSION['UserErrors'] = json_encode($this->errors);
        }
        header('Location: ../');
        return true;
    }

    private function executeAction($params){       
        switch ($params['action']) {
            case 'remove':
                (new UserModel())->removeUser($params['id']);
                break;
            case 'add':
                if ($_GET['password'] != $params['confirm_password']) {
                    $this->errors[] = 'Les mots de passe ne correspondent pas.';
                    break;
                }
                (new UserModel())->addUser($params['login'],$params['password']);
                break;

        }
    }
}