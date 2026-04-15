<?php

namespace Controllers;

use Override\ControllerOverride;
class UshiController extends ControllerOverride
{
    protected string $name;

    public function __construct()
    {
        $this->template = 'index.php';
        $this->addCSS('styles.css');
        $this->setTitle('CV');
        $this->addJS('chaise', true);
        $this->addJS('https://code.jquery.com/jquery-3.7.1.min.js');
        $this->addJS('soundwaves');
    }

    protected function getSiteName(): string
    {
        return 'Ushi Test';
    }

    # [Route('/ushi-test', 'app_ushitest')]
    public function ushitest(): bool
    {
        $this->renderTemplate();
        return true;
    }

}
