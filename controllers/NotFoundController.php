<?php

class NotFoundController extends ControllerCore
{
    protected string $name;

    public function __construct()
    {
        $this->template = '404.php';
        $this->addCSS('styles.css');
        $this->setTitle('404');
    }

    #[Route('/404', 'notFound')]
    public function notFound(): bool
    {
        $this->renderTemplate();
        return true;
    }

}
