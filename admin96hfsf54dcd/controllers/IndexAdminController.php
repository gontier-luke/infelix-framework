<?php 

class IndexAdminController extends AdminControllerCore{
    protected string $name;

    public function __construct()
    {
        $this->template = 'index.php';
    }

    public function run(): bool
    {
        if (!key_exists('token',$_SESSION)){
            header('Location: ./login/');
            die;
        }
        if( $_SESSION['token'] != (new LoginAdminController())->getToken($_SESSION['login'])) {
            header('Location: ./login/');
            die;
        }
        $this->renderTemplate($this->getTemplateVariables());
        return true;
    } 

    private function getTemplateVariables():array{
        
        $owner = (new UserModel())->isOwner($_SESSION['login']);
        $vars = [
            'title' => 'Espace administrateur',
            'owner' => $owner,
        ];

        if ($owner) {
            $vars['adminUsers'] =  (new UserModel())->getAllUser();
            $vars['addUserForm'] =  $this->getUserForm();
        }
        $vars['ipAdresses'] =  (new IpAdressesModel())->getAllIp();
        $vars['questions'] =  (new QuestionModel())->getAllQuestion();
        $vars['addQuestionForm'] =  $this->getQuestionForm();
        $vars['addDesacIpForm'] =  $this->getDesacIpForm();

        $vars['userErrors'] = [];
        if (isset($_SESSION['userErrors']) && json_decode($_SESSION['userErrors']) != []) {
            $vars['userErrors'] = json_decode($_SESSION['userErrors']);
            unset($_SESSION['userErrors']);
        }
        $vars['questionErrors'] = [];
        if (isset($_SESSION['questionErrors']) && json_decode($_SESSION['questionErrors']) != []) {
            $vars['questionErrors'] = json_decode($_SESSION['questionErrors']);
            unset($_SESSION['questionErrors']);
        }

        $vars['ipErrors'] = [];
        if (isset($_SESSION['ipErrors']) && json_decode($_SESSION['ipErrors']) != []) {
            $vars['ipErrors'] = json_decode($_SESSION['ipErrors']);
            unset($_SESSION['ipErrors']);
        }
       
        return $vars ;
    }

    private function getQuestionForm():string{
        $formBuilder = new FormBuilder();
        $formBuilder
            ->setAction('./question/index.php')
            ->setMethod('POST')
            ->setClass('form')
            ->add('question','Intitulé de la question', InputTypeEnum::GROUP,false,'', [
                'class' => 'question_group', 
                'children' => [
                    new FormInput('libelle','', InputTypeEnum::TEXTAREA,false,'', ['class' => 'big-textarea']),
                    new FormInput('image[0]','Ajouter des images à la question', InputTypeEnum::IMAGE,false),
                    new FormInput('imageDescription[0]','Description de l’image <small>(facultatif)</small>', InputTypeEnum::TEXTAREA,false),
                    new FormInput('image[1]','', InputTypeEnum::IMAGE,false),
                    new FormInput('imageDescription[1]','Description de l’image <small>(facultatif)</small>', InputTypeEnum::TEXTAREA,false),
                    new FormInput('action','', InputTypeEnum::HIDDEN,true,'add'),
                    new FormInput('submit','', InputTypeEnum::SUBMIT,true,'Ajouter'),
                ]
            ])
            ->add('reponse','Réponses autorisées :', InputTypeEnum::GROUP,false,'', [
                'class' => 'reponse_group',
                'children' => [
                    new FormInput('questionType','Texte libre', InputTypeEnum::RADIO,false,QuestionTypeEnum::FREE_TEXT->value, ['id' => QuestionTypeEnum::FREE_TEXT->value]),
                    new FormInput('questionType','Slider', InputTypeEnum::RADIO,false, QuestionTypeEnum::SLIDER->value, ['id' => QuestionTypeEnum::SLIDER->value]),
                    new FormInput('slider','Max :', InputTypeEnum::NUMBER,false, '', ['id' => 'text']),
                    new FormInput('questionType','Réponse sous forme d’images :', InputTypeEnum::RADIO,false, QuestionTypeEnum::MULTI_IMAGE->value, ['id' => QuestionTypeEnum::MULTI_IMAGE->value]),
                    new FormInput('imageResp[0]','', InputTypeEnum::IMAGE,false, '', ['class' => 'imageResp']),
                    new FormInput('imageRespDesc[0]','', InputTypeEnum::TEXT,false, '', ['placeholder' => 'Description de l’image']),
                    new FormInput('imageResp[1]','', InputTypeEnum::IMAGE,false, '', ['class' => 'imageResp']),
                    new FormInput('imageRespDesc[1]','', InputTypeEnum::TEXT,false, '', ['placeholder' => 'Description de l’image']),
                    new FormInput('imageResp[2]','', InputTypeEnum::IMAGE,false, '', ['class' => 'imageResp']),
                    new FormInput('imageRespDesc[2]','', InputTypeEnum::TEXT,false, '', ['placeholder' => 'Description de l’image']),
                    new FormInput('imageResp[3]','', InputTypeEnum::IMAGE,false, '', ['class' => 'imageResp']),
                    new FormInput('imageRespDesc[3]','', InputTypeEnum::TEXT,false, '', ['placeholder' => 'Description de l’image']),
                    new FormInput('questionType','Réponse sous forme de texte à choisir :', InputTypeEnum::RADIO ,false, QuestionTypeEnum::TEXT_CHOICE->value, ['id' => QuestionTypeEnum::TEXT_CHOICE->value]),
                    new FormInput('choiseText[0]','', InputTypeEnum::TEXT,false, '', ['placeholder' => 'Ecrire la réponse à choisir ici']),
                    new FormInput('choiseText[1]','', InputTypeEnum::TEXT,false, '', ['placeholder' => 'Ecrire la réponse à choisir ici']),
                    new FormInput('choiseText[2]','', InputTypeEnum::TEXT,false, '', ['placeholder' => 'Ecrire la réponse à choisir ici']),
                    new FormInput('choiseText[3]','', InputTypeEnum::TEXT,false, '', ['placeholder' => 'Ecrire la réponse à choisir ici']),
                ]
            ])
            ->add('response[0]','',InputTypeEnum::HIDDEN)
            ->add('response[1]','',InputTypeEnum::HIDDEN)
            ->add('response[2]','',InputTypeEnum::HIDDEN)
            ->add('response[3]','',InputTypeEnum::HIDDEN)
            ->add('id','',InputTypeEnum::HIDDEN);

        return $formBuilder->renderForm();
    }
    
    private function getUserForm():string
    {
        $formBuilder = new FormBuilder();
        $formBuilder
            ->setAction('./user/index.php')
            ->setMethod('GET')
            ->setClass('form')
            ->add('login','Identifiant', InputTypeEnum::TEXT,true)
            ->add('password','Mot de passe', InputTypeEnum::PASSWORD,true,'', ['pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$'])
            ->add('confirm_password','Confirmation mot de passe', InputTypeEnum::PASSWORD,true,'', ['pattern' => '^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$'])
            ->add('action','', InputTypeEnum::HIDDEN,true,'add')
            ->add('submit','', InputTypeEnum::SUBMIT,true,'Ajouter');

        return $formBuilder->renderForm();
    }

    private function getDesacIpForm():string
    {
        $hours['options'] = [];
        for ($i = 0; $i <= 24; $i++) {
            $hours['options'][$i] = $i . ' heure';
            if ($i > 1) {
                $hours['options'][$i] .= 's';
            }
        }
        $minutes['options'] = [];

        for ($i = 0; $i < 60; $i++) {
            $minutes['options'][$i] = $i . ' minute';
            if ($i > 1) {
                $minutes['options'][$i] .= 's';
            }
        }

        $secondes['options'] = [];

        for ($i = 0; $i < 60; $i++) {
            $secondes['options'][$i] = $i . ' seconde';
            if ($i > 1) {
                $secondes['options'][$i] .= 's';
            }
        }

        $formBuilder = new FormBuilder();
        $formBuilder
            ->setAction('./secuIp/index.php')
            ->setMethod('POST')
            ->setClass('form form-tempo')
            ->add('action','Augmenter temporairement la limite de personnes pouvant répondre de cette durée :', InputTypeEnum::HIDDEN,true,'desactivate')
            ->add('tempoGroup', '', InputTypeEnum::GROUP,false,'', [
                'class' => 'tempo_group',
                'children' => [
                    new FormInput('tempo[0]','', InputTypeEnum::SELECT,false,'0',$hours),
                    new FormInput('tempo[1]','', InputTypeEnum::SELECT,false,'0',$minutes),
                    new FormInput('tempo[2]','', InputTypeEnum::SELECT,false,'0',$secondes),
                    new FormInput('submit','', InputTypeEnum::SUBMIT,true,'Valider'),
                ]
            ]);
        return $formBuilder->renderForm();
    }
}
