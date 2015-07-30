<?php

require_once __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/../../../config/constants.php';


$app = new Laravel\Lumen\Application(
	realpath(__DIR__.'/../../../')
);

$app->withFacades();
$app->withEloquent();

$app->singleton(
    'Illuminate\Contracts\Debug\ExceptionHandler',
    'Util\Exceptions\Handler'
);

$app->singleton(
    'Illuminate\Contracts\Console\Kernel',
    'Util\Console\Kernel'
);

require __DIR__.'/../core/routes.php';
require __DIR__.'/routes.php';

$db = new HoneyBase\Core\Model\MysqlAdaptor();

return $app;
