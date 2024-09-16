<?php

class IndexController extends ControllerCore
{
    protected string $name;

    public function __construct()
    {
        $this->template = 'index.php';
        $this->addCSS('styles.css');
        $this->setTitle('Infelix Commentator - Home');
    }

    #[Route('/', 'app_index')]
    public function index(): bool
    {
        $this->renderTemplate();
        return true;
    }

    // #[Route('/accueil-{couleur}', 'app_blue_index')]
    // public function colored(string $couleur): bool
    // {
    //     $this->renderTemplate($this->user);
    //     return true;
    // }

}
