<?php

require '../vendor/autoload.php';

use SpriiTestApp\core\controller\DefaultController;
use SpriiTestApp\http\Request;
use SpriiTestApp\dba\DBA;

$config = new \SimpleXMLElement(file_get_contents('../config.xml'));

$route = filter_input(INPUT_GET, 'r', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$matchedRouteConfig = $config->routes->default;
foreach ($config->routes->route as $routeConfig) {

    if ((string) $routeConfig->path === "/{$route}") {
        $matchedRouteConfig = $routeConfig;
        break;
    }
}

$controllerClassname = (string) $routeConfig->controller;

$controller = new $controllerClassname;
if ($controller instanceof SpriiTestApp\core\controller\Controller) {

    $controller->setConfig($config);
    DBA::instance()->setConfig($config);
    
    $request = new Request();
    $request->init();
    $controller->setTemplate("{$config->installPath}/{$matchedRouteConfig->template}");
    $response = $controller->execute($request);
    $response->sendHeaders(array(
        'Content-type' => $matchedRouteConfig->contentType,
    ));
    $response->send();
}
