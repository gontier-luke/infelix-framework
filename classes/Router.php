<?

namespace Classes;

use Classes\Collections\ObjectCollection;
use Enum\LangEnum;
use Exceptions\RouteException;
use Repositories\Configuration;
use Repositories\PageRepository;
use Services\AdminService;

class Router {

    /** @var ObjectCollection<Route> */
    public static ObjectCollection $routes;

    /** @var string */
    public static string $adminPrefix;

    /** @var bool */
    private static bool $useLangSystem;

    public function __construct() {
        self::$routes = new ObjectCollection(Route::class);
        self::$adminPrefix = 'admin';
        self::$useLangSystem = Configuration::get('useLangInUrl') === '1';
        $this->getRoute();
        // dd(self::$routes->toArray());
    }

    /**
     * Vérifie si la langue est présente dans le chemin et la définit dans la session
     * @param string $path
     * @return bool
     */
    private function setLang(string &$path): bool {
        if(!self::$useLangSystem) {
            if(!isset($_SESSION['lang']) || !is_null($_SESSION['lang']) ) {
                $_SESSION['lang'] = null;
            }
            return true;
        }
        preg_match('/(\/('.implode('|', LangEnum::getCodes()).')).*\//', $path, $matches );
        if(!empty($matches)) {
            $_SESSION['lang'] = $matches[2];
            $path = str_replace($matches[1], '', $path);
            return true;
        }
        $_SESSION['lang'] = Configuration::get('defaultLang');
        return false;
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
        $isActive = true;
        $oPage = PageRepository::findByAppName($name);
        if($oPage && $oPage->isActive() === false) {
            $isActive = false;
        }
        self::$routes->add(new Route($path, $method, $controller, $name, $this->getRouteParams($path), $isActive));
    }

    /**
     * Prend en charge une requête entrante
     * 
     * Règles :
     * - Si la route est une route admin inexistante, la requête est redirigée vers la page de connexion
     * - Si la route commence par le préfixe admin défini pour ce projet, la requête est traitée comme une requête admin
     * - Si la route correspon à une route contetenant le préfixe 'admin', une erreur 404 est renvoyée pour éviter l'accès non autorisé
     * - Si le site est en mode maintenance, la requête est redirigée vers la page de maintenance
     * @param string $path
     * @throws RouteException
     * @return void
     */
    public function handleRequest(string $path): void
    {
        $adminUrlprefix = ADMIN_INDEX_PATH;
        $adminLangPath = $path;
        if(self::$useLangSystem) {
            if(!isset($_SESSION['lang']) || is_null($_SESSION['lang']) ) {
                $_SESSION['lang'] = Configuration::get('defaultLang');
            }
            $adminLangPath = '/' . $_SESSION['lang'] . $path;
        }

        $params = [];
        $route = $this->getRouteByPath($path, $params);
        if($route->isEmpty()) {
            $path = '/404'; // Force not found
        }
        if(isset($pathExploded[1]) && $pathExploded[1] === self::$adminPrefix) {
            $this->handleAdminRequest($adminLangPath);
            return;
        }
        if(Configuration::get('maintenance') === '1') {
            /** @var \Controllers\MaintenanceController $controller */
            $controller = ControllerCore::getInstanceByName("maintenance");
            $controller->maintenance();
            return;
        }

        $this->getRequestResult($path);

        return;
    }

    public function getAllRoutes(): ObjectCollection {
        return self::$routes;
    }

    private function getRoute(): void {
        foreach(Parser::parseRoutes() as $controller => $routes){
            $controllerName = $this->getControllerName($controller);
            foreach($routes as $route) {
                if(!isset($route['path']) || !isset($route['method']) || !isset($route['name'])) {
                    throw new RouteException(message: 'Route path, method or name is missing there : '. $controller);
                }
                $this->addRoute($route['path'], $route['method'], $controllerName, $route['name']);
            }
        }
    }

    private function getControllerName(string $file) {
        $controllerName = lcfirst(str_replace('Controller.php', '', $file));
        return $controllerName;
    }


    /**
     * Génère une URL à partir du nom de la route et des paramètres
     * 
     * Si des langues sont utilisées dans les URL, la langue actuelle de la session sera incluse dans l'URL générée.
     * 
     * @param string $name
     * @param array $params
     * @return string
     */
    public static function generateUrl(string $name, array $params = []): string {
        $routes = self::$routes->filter(function($route) use ($name) {
            return $route->getAppName() === $name;
        });
        if($routes->isEmpty()) {
            $routes = self::$routes->filter(function($route) {
                return $route->getAppName() === 'notFound';
            });
        }

        $path = $routes->get(0)->getPath();
        if($params != []) {
            foreach($params as $key => $value) {
                if(!is_scalar($value)) {
                    throw new RouteException("Route parameter must be a scalar value. Route: $name, Key: $key, Value: " . print_r($value, true));
                }
                $path = str_replace('{' . $key . '}', $value, $path);
            }
        }

        if(str_starts_with($path, '/' . self::$adminPrefix)) {
            $path = str_replace('/' . self::$adminPrefix , ADMIN_INDEX_PATH, $path);
        }

        $realPath = str_replace('//', '/', $_ENV["PROJECT_ROOT"] . $_SESSION['lang'] . $path);

        return $realPath; 
    }

    /**
     * @param string $path
     * @return ObjectCollection<Route>
     * @throws RouteException
     */
    private function getRouteByPath(string $path, array &$params) : ObjectCollection
    {
        $filtered = self::$routes->filter(function($route) use ($path) {
            if($route->isAdminRoute && str_replace(ADMIN_INDEX_PATH, self::$adminPrefix, $path) === $route->getPath()) {
                return true;
            } 
            return $route->getPath() === $path && (!$route->isActive && AdminService::isAdminLogged($_SESSION) || $route->isActive);
        });
        if($filtered->isEmpty()) {
            foreach(self::$routes->getAll() as $route) {
                /** @var Route $route */
                // dump($route->toArray());
                $routeParams = $route->getParams();
                if(!empty($routeParams)) {
                    if($route->isAdminRoute){
                        $path = str_replace(ADMIN_INDEX_PATH, self::$adminPrefix, $path);
                    }
                    $regex = preg_quote($route->getPath(), '/');
                    foreach($routeParams as $arg) {
                        $regex = str_replace('\{' . $arg . '\}', '(.*)', $regex);
                    }
                    $regex = '/^' . $regex . '$/';
                    $newParams = [];
                    if(preg_match( $regex, $path, $newParams) == 1 && !empty($newParams)) {
                        
                        if(!(!$route->isActive && AdminService::isAdminLogged($_SESSION) || $route->isActive)) {
                            continue;
                        }
                        array_shift($newParams);
                        foreach($routeParams as $index => $argName) {
                            if(!array_key_exists($index, $newParams)) {
                                throw new RouteException("Route parameter missing: " . $argName);
                            }
                            $params[$argName] = $newParams[$index];
                        }
                        $filtered->add($route);
                    }
                }
            }
        }
        if($filtered->count() > 1) {
            $moreDetailedRoute = $this->getMoreDetailedRoute($filtered);
            if(!is_null($moreDetailedRoute)) {
                $filtered = new ObjectCollection(Route::class);
                $filtered->add($moreDetailedRoute);
            }
        }
        return $filtered;
    }

    private function getMoreDetailedRoute(ObjectCollection $routes): ?Route
    {
        $filtered = $routes->filter(function($route) {
            return !empty($route->getParams());
        });
        $filtered = $filtered->sort(function($a, $b) {
            return count($b->getParams()) <=> count($a->getParams());
        });
        return $filtered->isEmpty() ? null : $filtered->first();
    }

    public static function redirect(string $appName, array $params = []): never {
        self::redirectUrl(self::generateUrl($appName, $params));
        exit();
    }

    public static function redirectUrl(string $url): never {
        header('Location: '. $url);
        exit();
    }


    /**
     * Prend en charge une requête admin
     * @param string $path
     * @throws RouteException
     * @return bool Indique si la requête a été traitée avec succès ou renvoyée à la page de connexion
     */
    private function handleAdminRequest(string $path): bool {
        if(ADMIN_INDEX_PATH === '/admin') {
            throw new RouteException("Ne pas configurer ADMIN_INDEX_PATH à '/admin'.");
        }
        // Remove admin prefix from path
        $prefixPos = strpos($path, ADMIN_INDEX_PATH);
        if($prefixPos === false) {
            throw new RouteException("Le chemin admin ne commence pas par le préfixe admin attendu.");
        }
        $path = substr($path, strlen(ADMIN_INDEX_PATH) + $prefixPos);
        $prefixToAdd = '/admin';
        if(!str_starts_with($path, '/')) {
            $prefixToAdd .= '/';
        }
        self::$useLangSystem = false;
        if(!AdminService::isAdminLogged($_SESSION) && $path !== '/login') {
            self::redirectAdmin('app_admin_login');
            return false;
        }
        $path = $prefixToAdd . $path;

        $this->getRequestResult($path);
        return true;
    }

    private function getRequestResult(string $path): void {
        $params = [];
        $redirectLang = !$this->setLang($path);
        $filtered = $this->getRouteByPath($path, $params);
        $controller = ControllerCore::getInstanceByName("NotFound");
        $function = "notFound";
        
        if (!$filtered->isEmpty()) {
            if($filtered->count() > 1) {
                dump($filtered->toArray());
                throw new RouteException("Multiple routes found for path: $path");
            }
            /**  @var Route $route */
            $route = $filtered->get(0);
            if($redirectLang) {
                self::redirect($route->getAppName(), $params);
                return;
            }
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

    public static function redirectAdmin(string $appName, array $params = []): never {
        $url = self::generateAdminUrl($appName, $params);

        self::redirectUrl($url);
        throw new RouteException("Redirection vers une   page admin a échoué " . $url);
    }

    public static function generateAdminUrl(string $name, array $params = []): string {
        $routes = self::$routes->filter(function($route) use ($name) {
            return $route->getAppName() === $name;
        });
        if($routes->isEmpty()) {
            $routes = self::$routes->filter(function($route) {
                return $route->getAppName() === 'notFound';
            });
        }

        $path = $routes->get(0)->getPath();
        if($params != []) {
            foreach($params as $key => $value) {
                $path = str_replace('{' . $key . '}', $value, $path);
            }
        }

        if(str_starts_with($path, '/' . self::$adminPrefix)) {
            $path = str_replace('/' . self::$adminPrefix, ADMIN_INDEX_PATH, $path);
        }

        $realPath = str_replace('//', '/', $_ENV["PROJECT_ROOT"] . $path);
        return $realPath; 
    }
}