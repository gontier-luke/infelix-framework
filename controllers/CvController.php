<?php

class CvController extends ControllerCore
{
    protected string $name;

    public function __construct()
    {
        $this->template = 'global.php';
        $this->addCSS('styles.css');
        $this->setTitle('CV');
    }

    protected function getSiteName(): string
    {
        return 'Luke Gontier';
    }

    #[Route('/cv', 'app_cv')]
    public function cv(): bool
    {
        $this->renderTemplate();
        return true;
    }

}
