<?php
require __DIR__ . '/vendor/autoload.php';

use Klein\Klein as Router;
use Rss\Controller\ErrorController;
use Rss\Controller\HomeController;

$twig_path = __DIR__ . '/views';

$app = array(
    'controller.home' => new HomeController($twig_path),
    'controller.error' => new ErrorController($twig_path),
);


$router = new Router();

$router->respond('GET', '/', function () use ($app) {
    return $app['controller.home']->indexAction();
});

$router->respond('404', function () use ($app) {
    return $app['controller.error']->_404();
});


$router->dispatch();

