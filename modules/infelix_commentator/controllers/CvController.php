<?php

namespace Controllers;

use Entities\BasicSliderEntity;
use Models\FormationModel;
use Override\ControllerOverride;
use Services\SliderService;

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
        $formationSlider = SliderService::generateSliderFromModel(FormationModel::class, 'sliderText');
        $this->renderTemplate([ 'formationSlider' => $formationSlider ]);
        return true;
    }

}
