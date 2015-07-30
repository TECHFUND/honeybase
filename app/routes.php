<?php

$app->routeMiddleware([
]);


$app->get('/', ['uses'=>'App\Controller\HomeController@index']);
