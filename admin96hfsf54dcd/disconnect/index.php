<?php 

session_start();

foreach(glob('../classes/*.php') as $fileName){

    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

foreach(glob('../modeles/*.php') as $fileName){

    if (!str_contains($fileName,'index.php')) {
        require_once $fileName;
    }
}

if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    die;
}



if (!file_exists(BASE_PATH.'/.env')) {
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

$controller = AdminControllerCore::getInstanceByName(basename(__DIR__));
$controller->run();