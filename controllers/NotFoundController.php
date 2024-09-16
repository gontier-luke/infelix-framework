<?php

class NotFoundController extends ControllerCore
{
    protected string $name;

    public function __construct()
    {
        $this->template = '404.php';
        $this->addCSS('style.css');
        $this->setTitle('Infelix Commentator - 404');
    }

    public function notFound(): bool
    {
        $this->renderTemplate();
        return true;
    }

}
