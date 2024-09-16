<?

use PSpell\Config;

class Router {

    private ObjectCollection $routes;

    public function __construct() {
        $this->routes = new ObjectCollection(Route::class);
        $this->getRoute();
    }

    public function addRoute(string $path, string $method, string $controller, string $name) {
        $this->routes->add(new Route($path, $method, $controller, $name));
    }

    public function handleRequest(string $path): void
    {
        if(Configuration::get('maintenance') === '1') {
            /** @var MaintenanceController */
            $controller = ControllerCore::getInstanceByName("maintenance");
            $controller->maintenance();
            return;
        }
        $filtered = $this->routes->filter(function($route) use ($path) {
            return $route->getPath() === $path;
        });
        $controller = ControllerCore::getInstanceByName("NotFound");
        $function = "notFound";
        if (!$filtered->isEmpty()) {
            if($filtered->count() > 1) {
                throw new RouteException("Multiple routes found for path: $path");
            }
            $route = $filtered->get(0);
            $controller = ControllerCore::getInstanceByName($route->getController());
            if($controller::class === "NotFoundController") {
                throw new RouteException("Controller not found: " . $route->getController());
            }
            $function = $route->getMethod();
        }

        if(!method_exists($controller, $function)) {
            throw new RouteException('Controller ('.$controller::class.') function failed: '.$function);
        }
        $controller->$function();
        return;
    }

    public function getAllRoutes() {
        return $this->routes;
    }

    private function getRoute() {
        foreach(Parser::parseRoutes() as $controller => $routes){
            $controllerName = $this->getControllerName($controller);
            foreach($routes as $route) {
                if(!isset($route['path']) || !isset($route['method']) || !isset($route['name'])) {
                    throw new RouteException('Route path, method or name is missing there : '. $controller);
                }
                $this->addRoute($route['path'], $route['method'], $controllerName, $route['name']);
            }
        }
    }

    private function getControllerName(string $file) {
        $controllerName = str_replace('Controller.php', '', $file);
        return $controllerName;
    }

}