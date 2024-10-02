<?php

class RandomController extends ControllerCore
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

    #[Route('/Lucky', 'app_lucky')]
    public function cv(): bool
    {
        $this->redirect('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        return true;
    }

}
