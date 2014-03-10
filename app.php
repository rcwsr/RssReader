<?php
require __DIR__ . '/vendor/autoload.php';

use Klein\Klein as Router;
use Rss\Controller\AjaxController;
use Rss\Controller\ErrorController;
use Rss\Controller\HomeController;

//$twig_path = __DIR__ . '/views';

$config = json_decode(file_get_contents(__DIR__ . '/config/config.json'), true);


$home_controller = new HomeController($config);
$error_controller = new ErrorController($config);
$ajax_controller = new AjaxController($config);


$router = new Router();

$router->respond('GET', '/', function () use ($home_controller) {
    return $home_controller->indexGetAction();
});

$router->respond('POST', '/', function () use ($home_controller) {
    return $home_controller->indexPostAction();
});

//Ajax
$router->respond('POST', '/ajax/addfeed', function () use ($ajax_controller) {
    return $ajax_controller->addFeedAction();
});
$router->respond('GET', '/ajax/publicfeeds/[i:limit]', function ($request) use ($ajax_controller) {
    return $ajax_controller->loadPublicFeedsInclude($request->limit);
});
$router->respond('GET', '/ajax/publicfeeds/all', function () use ($ajax_controller) {
    return $ajax_controller->loadPublicFeedsInclude();
});
$router->respond('GET', '/ajax/userfeeds/[i:limit]', function ($request) use ($ajax_controller) {
    return $ajax_controller->loadMyFeedsInclude($request->limit);
});
$router->respond('GET', '/ajax/userfeeds/all', function () use ($ajax_controller) {
    return $ajax_controller->loadMyFeedsInclude();
});


$router->respond('404', function () use ($error_controller) {
    return $error_controller->_404();
});

$router->dispatch();

