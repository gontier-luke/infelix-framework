<?php 

class DisconnectAdminController extends AdminControllerCore{
    private array $errors = [];

    public function __construct()
    {
        $this->template = 'index.php';
    }

    public function run(): bool
    {
        if(key_exists('token', $_SESSION)){
            session_destroy();
        }

        header('Location: ../');

        return true;
    }    
}
