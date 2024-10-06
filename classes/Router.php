<?

use PSpell\Config;

class Router {

    /** @var ObjectCollection<Route> */
    private static ObjectCollection $routes;

    public function __construct() {
        self::$routes = new ObjectCollection(Route::class);
        $this->getRoute();
    }

    private function getRouteParams(string $path) {
        $params = [];
        preg_match_all('/\{([^\}]*)\}/', $path, $matches);
        
        if(!empty($matches[1])) {
            foreach($matches[1] as $match) {
                $params[] = $match;
            }
        }

        return $params;
    }

    public function addRoute(string $path, string $method, string $controller, string $name) {
        self::$routes->add(new Route($path, $method, $controller, $name, $this->getRouteParams($path)));
    }

    public function handleRequest(string $path): void
    {
        if(Configuration::get('maintenance') === '1') {
            /** @var MaintenanceController */
            $controller = ControllerCore::getInstanceByName("maintenance");
            $controller->maintenance();
            return;
        }
        $params = [];
        $filtered = $this->getRouteByPath($path, $params);
        $controller = ControllerCore::getInstanceByName("NotFound");
        $function = "notFound";
        if (!$filtered->isEmpty()) {
            if($filtered->count() > 1) {
                throw new RouteException("Multiple routes found for path: $path");
            }
            /**  @var Route $route */
            $route = $filtered->get(0);
            $controller = ControllerCore::getInstanceByName($route->getController());
            if(is_null($controller)) {
                throw new RouteException("Controller not found: " . $route->getController());
            }
            $function = $route->getMethod();
        }

        if(!method_exists($controller, $function)) {
            throw new RouteException('Controller ('.$controller::class.') function failed: '.$function);
        }
        $controller->$function(...$params);
        return;
    }

    public function getAllRoutes() {
        return self::$routes;
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
        $controllerName = lcfirst(str_replace('Controller.php', '', $file));
        return $controllerName;
    }

    public static function generateUrl(string $name, array $params = []): string {
        $routes = self::$routes->filter(function($route) use ($name) {
            return $route->getAppName() === $name;
        });
        if($routes->isEmpty()) {
            $routes = self::$routes->filter(function($route) {
                return $route->getAppName() === 'notFound';
            });
        }
        return $routes->get(0)->getPath(); 
    }

    /**
     * @param string $path
     * @return ObjectCollection<Route>
     */
    private function getRouteByPath(string $path, array &$params) : ObjectCollection
    {
        $filtered = self::$routes->filter(function($route) use ($path) {
            return $route->getPath() === $path;
        });
        if($filtered->isEmpty()) {
            foreach(self::$routes->getAll() as $route) {
                /** @var Route $route */
                // dump($route->toArray());
                $routeParams = $route->getParams();
                if(!empty($routeParams)) {
                    $regex = '/'.preg_quote($route->getPath(), '/').'/';
                    foreach($routeParams as $arg) {
                        $regex = str_replace('\{' . $arg . '\}', '(.*)', $regex);
                    }
                    $newParams = [];
                    if(preg_match( $regex, $path, $newParams) == 1 && !empty($newParams)) {
                        array_shift($newParams);
                        $params = $newParams;
                        $filtered->add($route);
                    }
                }
            }
        }
        return $filtered;
    }
}