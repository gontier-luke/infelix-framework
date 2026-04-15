<?php

namespace Controllers;

use Override\ControllerOverride;
class CvController extends ControllerOverride
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

    # [Route('/cv', 'app_cv')]
    public function cv(): bool
    {
        $formationSlider = (new Slider(FormationModel::class))->renderSlider();
        $this->renderTemplate([ 'formationSlider' => $formationSlider ]);
        return true;
    }

}
