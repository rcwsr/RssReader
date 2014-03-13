<?php
require __DIR__ . '/vendor/autoload.php';

use Klein\Klein as Router;
use Rss\Controller\AjaxController;
use Rss\Controller\ErrorController;
use Rss\Controller\HomeController;

//$twig_path = __DIR__ . '/views';

$config = json_decode(file_get_contents(__DIR__ . '/config/config.json'), true);

//Instantiate controllers
$home_controller = new HomeController($config);
$error_controller = new ErrorController($config);
$ajax_controller = new AjaxController($config);

//Load router
$router = new Router();

//Homepage
$router->respond('GET', '/', function () use ($home_controller) {
    return $home_controller->indexGetAction();
});

//Ajax
$router->respond('POST', '/ajax/addfeed', function () use ($ajax_controller) {
    return $ajax_controller->addFeedAction();
});
$router->respond('POST', '/ajax/deletefeedcheck', function () use ($ajax_controller) {
    return $ajax_controller->deleteFeedConfirmationAction();
});
$router->respond('POST', '/ajax/deletefeed', function () use ($ajax_controller) {
    return $ajax_controller->deleteFeedAction();
});
$router->respond('GET', '/ajax/publicfeeds/[i:limit]', function ($request) use ($ajax_controller) {
    return $ajax_controller->loadPublicFeedsIncludeAction($request->limit);
});
$router->respond('GET', '/ajax/publicfeeds/all', function () use ($ajax_controller) {
    return $ajax_controller->loadPublicFeedsIncludeAction();
});

$router->respond('GET', '/ajax/userfeeds/[i:limit]', function ($request) use ($ajax_controller) {
    return $ajax_controller->loadUserFeedsIncludeAction($request->limit);
});
$router->respond('GET', '/ajax/userfeeds/all', function () use ($ajax_controller) {
    return $ajax_controller->loadUserFeedsIncludeAction();
});
$router->respond('POST', '/ajax/getfeed', function () use ($ajax_controller) {
    return $ajax_controller->getFeedAction();
});


$router->respond('404', function () use ($error_controller) {
    return $error_controller->errorAction(404, "Page could not be found");
});

$router->respond('403', function () use ($error_controller) {
    return $error_controller->errorAction(404, "You are not authorised to visit this page");
});

$router->respond('500', function () use ($error_controller) {
    return $error_controller->errorAction(404, "Something went wrong!");
});

$router->dispatch();

