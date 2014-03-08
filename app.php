<?php
require 'vendor/autoload.php';

use Pux\Mux as Router;


$router = new Router;

$router->get('/',['Rss\Controller\TestController', 'testMethod']);

try{
    $route = $router->dispatch($_SERVER['REQUEST_URI']);
    echo Pux\Executor::execute($route);
}
catch(ReflectionException $re){
    echo "Could not find page";
}
