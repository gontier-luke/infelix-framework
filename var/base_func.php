<?php 

$explodedPath = explode('/',dirname(__FILE__));
array_pop($explodedPath);
$basePath = implode('/',$explodedPath) . '/';
define('BASE_PATH', $basePath);
define('ADMIN_PATH',$basePath.'/admin96hfsf54dcd/');
define('IMAGE_LINK','/image/');

if (!file_exists(BASE_PATH . '/.env')) {
    throw new Exception("Le fichier .env n'existe pas.");
}

$file = file(BASE_PATH.'/.env');
foreach ($file as $line) {
    if (strpos(trim($line), '#') === 0) {
        continue;
    }

    list($key, $value) = explode(':', $line, 2);
    $key = trim($key);
    $value = trim($value);

    if (!array_key_exists($key, $_ENV)) {
        putenv(sprintf('%s=%s', $key, $value));
        $_ENV[$key] = $value;
    }
}

function dump(...$vars){
    foreach($vars as &$var){
        if(is_string($var)){
            $var = sanitize($var);
        }
        $var = [
            'type' => gettype($var),
            'value' => $var
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


function displayArray(array $array, ) {
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
