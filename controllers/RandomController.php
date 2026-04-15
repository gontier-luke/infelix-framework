<?php

namespace Controllers;

use Override\ControllerOverride;
class RandomController extends ControllerOverride
{
    protected string $name;

    public function __construct()
    {
        $this->template = 'global.php';
        $this->addCSS('styles.css');
        $this->setTitle('Wtf');
    }

    protected function getSiteName(): string
    {
        return 'Luke Gontier';
    }

    #[Route('/Lucky', 'app_lucky')]
    public function lucky(): bool
    {
        $this->redirect('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        return true;
    }

}
