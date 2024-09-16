<?php

class QuestionAdminController extends AdminControllerCore
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
            if (key_exists('action', $_GET)) {
                $this->executeAction($_GET);
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        
        if ($this->errors != []) {
            $_SESSION['questionErrors'] = json_encode($this->errors);
        }
        
        header('Location: ../');
        return true;
    }

    private function executeAction($params)
    {
        switch ($params['action']) {
            case 'remove':

                (new QuestionModel())->removeQuestion($params['id']);
                break;
            case 'add':    
                if (!isset($params['questionType'])) {
                    $this->errors[] = 'Le type de question doit être sélectionné.';
                    break;
                }
                if ($params['libelle'] == '') {
                    $this->errors[] = 'La question ne peut pas être vide.';
                    break;
                }
                QuestionTypeEnum::tryFrom($params['questionType'])->addNewQuestion($params);
                break;
            case 'getQuestion' :
                echo json_encode((new QuestionModel())->getQuestion($params['id']));
                die;
            case 'update' :
                if (!isset($params['questionType'])) {
                    $this->errors[] = 'Le type de question doit être sélectionné.';
                    break;
                }
                if ($params['libelle'] == '') {
                    $this->errors[] = 'La question ne peut pas être vide.';
                    break;
                }
                QuestionTypeEnum::tryFrom($params['questionType'])->updateQuestion($params);
                break;
            }
    }
}
