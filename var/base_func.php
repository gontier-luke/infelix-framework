<?php 

use Classes\ModelCore;
use Services\ModuleService;

$explodedPath = explode('/',dirname(__FILE__));
array_pop($explodedPath);
define('INDEX_PATH', '/');
define('ADMIN_INDEX_PATH', '/admin96hfsf54dcd');

$basePath = implode('/',$explodedPath) . INDEX_PATH;
define('BASE_PATH', $basePath);
define('ADMIN_PATH',$basePath. ADMIN_INDEX_PATH);
define('IMAGE_LINK','/image/');

if (!file_exists(BASE_PATH . '/.env')) {
    throw new Exception("Le fichier .env n'existe pas.");
}

$file = file(BASE_PATH.'/.env');
foreach ($file as $line) {
    if (strpos(trim($line), '#') === 0) {
        continue;
    }

    list($key, $value) = explode(':', $line, limit: 2);
    $key = trim($key);
    $value = trim($value);

    if (!array_key_exists($key, $_ENV)) {
        putenv(sprintf('%s=%s', $key, $value));
        $_ENV[$key] = $value;
    }
}

function dump(...$vars){
    if(getenv('DEBUG') !== 'y'){
        return;
    }
    $aVars = [];
    foreach($vars as &$var){
        $varType = gettype($var);
        switch(gettype($var)){
            case 'string':
                $var = sanitize($var);
                break;
            case 'boolean':
                $var = $var ? 'true' : 'false';
                break;
        }
        $aVars[] = [
            'type' => $varType,
            'value' => $var,
        ];
        
    }

    require BASE_PATH . 'templates/base_func/dump.php';
}

function dd(...$vars){
    dump(...$vars);
    die();
}

function sanitize($var){
    return '\'' . str_replace("\n", '</br>', htmlspecialchars($var)) . '\'';
}

function camelToSnake($input){
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
}

function snakeToCamel($input){
    return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $input))));
}


function displayArray(array $array ) {
    foreach($array as $key => $value) {
        displayCascade($value, $key);
    }
    echo "</ul>";
}

function displayCascade(mixed $value, mixed $key) {
    echo '<li style="color: white; list-style-type: none; padding-left: 5px"> ';
    if(is_array($value)) {
        echo "<ul style='color: orange; list-style-type: none; padding-left: 5px'> $key => (Array)";
        displayArray($value);
        echo '</li>';
        return;
    }
    if(is_object($value)) {
        echo "<ul style='color: orange'> $key => ";
        displayObject($value);
        echo '</li>';
        return;
    }
    echo $key .' => (' .gettype($value). ')  ' . $value;
    echo '</li>';

}

function displayObject(object $object, ) {
    echo '(' . $object::class . ')';
    foreach(get_object_vars($object) as $key => $value) {
        displayCascade($value, $key);
    }
    echo "</ul>";
}

function render(string $template, array $data = []){
    foreach($data as $key => $value){
        $$key = $value;
    }
    ob_start();
    require_once(BASE_PATH . 'templates/' . $template . '.php');
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

function checkTable(string $tableName, ?string $from = null): bool{
    // Vérifie si le module est actif
    /** @var string $modelClass */
    $modelClass = 'Models\\' . ucfirst(string: snakeToCamel($tableName)) . 'Model';
    if(!class_exists($modelClass)) {
        return false;
    }
    $modelService = new ModuleService();
    if($from !== null && !$modelService->isModuleActive($from)){
        return false;
    }

    try {
        ModelCore::checkTable(tableName: $tableName, className: $modelClass);
    } catch (Exception $e) {
        dd($e->getMessage());
        return false;
    }
    return true;
}

function getAllActiveModules(): array {
    checkTable('module');
    $moduleService = new ModuleService();
    return $moduleService->getActiveModules();
}

function installModule(string $moduleName): bool {
    checkTable('module');

    $installPath = BASE_PATH . 'modules/' . $moduleName . '/install.php';
    if(!file_exists($installPath)) {
        return false;
    }

    require_once($installPath);

    $moduleService = new ModuleService();
    $moduleService->installModule(settings: $settings);
    return true;
}
