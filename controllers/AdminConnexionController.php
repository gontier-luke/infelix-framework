<?php

namespace Controllers;

use Classes\AdminControllerCore;
use Classes\FormBuilder;
use Classes\Router;
use Enum\InputTypeEnum;
use Services\AdminService;

class AdminConnexionController extends AdminControllerCore
{
    # [Route('/admin', 'app_admin_index')]
    public function index(): bool{
        Router::redirect('app_admin_login');
        return true;
    }

    # [Route('/admin/login', 'app_admin_login')]
    public function login(): bool
    {
        if(AdminService::isAdminLogged($_SESSION)) {
            Router::redirect('app_admin_dashboard');
            return true;
        }
        $this->template = 'login.php';
        $this->setTitle('Admin Login');
        $formLogin = (new FormBuilder())
            ->setMethod('POST')
            ->add('username', 'Nom d\'utilisateur ou e-mail', InputTypeEnum::TEXT, true, extra: [
                'placeholder' => 'Entrez votre nom d\'utilisateur ou e-mail',
                'class' => 'rounded-md text-lg',
                'autofocus' => true,
                ])
            ->add('password', 'Mot de passe', InputTypeEnum::PASSWORD, true, extra: [
                'placeholder' => 'Entrez votre mot de passe',
                'class' => 'rounded-md text-lg',
                ])
            ->add('connexion', 'Se connecter', InputTypeEnum::SUBMIT, true)
            ->renderForm();
        $errorMessage = null;
        $formBgColor = 'bg-grey';
        if($_POST && isset($_POST['connexion'])){
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            try{
                if(AdminService::login($username, $password)) {
                    Router::redirect('app_admin_dashboard');
                    return true;
                }
                throw new \Exception("Nom d'utilisateur ou mot de passe incorrect.");
            }catch(\Exception $e){
                $errorMessage = $e->getMessage();
                $formBgColor = 'bg-red';
            }
        }
        $this->renderTemplate(['formLogin' => $formLogin, 'errorMessage' => $errorMessage, 'formBgColor' => $formBgColor ,'displayMenuFooter' => false]);
        return true;
    }

    # [Route('/admin/logout', 'app_admin_logout')]
    public function logout(): bool
    {
        AdminService::logout();
        Router::redirect('app_admin_login');
        return true;
    }
}
