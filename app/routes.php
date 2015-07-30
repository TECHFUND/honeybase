<?php

$app->routeMiddleware([
  "login" => "App\Middleware\LoginMiddleware"
]);


$app->get('/', ['uses'=>'App\Controller\HomeController@index']);
