<?php

class CvController extends ControllerCore
{
    protected string $name;

    public function __construct()
    {
        $this->template = 'global.php';
        $this->addCSS('style.css');
        $this->setTitle('Infelix Commentator - Home');
    }

    #[Route('/cv', 'app_cv')]
    public function cv(): bool
    {
        $this->renderTemplate();
        return true;
    }

}
