<?

class MaintenanceController extends ControllerCore
{
    protected string $name;

    public function __construct()
    {
        $this->template = 'maitenance.php';
        $this->addCSS('style.css');
        $this->setTitle('Infelix Commentator - Maintenance');
    }

    public function maintenance(): bool
    {
        $this->renderTemplate();
        return true;
    }

    protected function renderTemplate($variables = []): void
    {
        $this->addCSS('base.css');
        $siteRoot = $_ENV["PROJECT_ROOT"];
        $templatesRoot = BASE_PATH.'/templates/';
        $stylesheets = $this->stylesheets;
        $scripts = $this->scripts;
        $title = $this->title;

        if ($siteRoot[0] !== '/') {
            $siteRoot = '/' . $siteRoot;
        }

        $templateLink = $templatesRoot . $this->name . '/' . $this->template;
        if ($this->name == null || $this->template == null) {
            $templateLink = $templatesRoot . '/index/index.php';
        }
        
        require_once $templatesRoot . 'maintenance/maitenance.php';
    }

}