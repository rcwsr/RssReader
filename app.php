<?php
require __DIR__ . '/vendor/autoload.php';

use Klein\Klein as Router;
use Rss\Controller\ErrorController;
use Rss\Controller\HomeController;

//$twig_path = __DIR__ . '/views';

$config = json_decode(file_get_contents(__DIR__ . '/config/config.json'), true);


$home_controller = new HomeController($config);
$error_controller = new HomeController($config);


$router = new Router();

$router->respond('GET', '/', function () use ($home_controller) {
    return $home_controller->indexAction();
});

$router->respond('404', function () use ($error_controller) {
    return $error_controller->_404();
});

$router->dispatch();

