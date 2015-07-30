<?php

$app->routeMiddleware([
  'accessor' => 'Lib\HoneyBase\Middleware\AccessorMiddleware'
]);

$app->group(['middleware' => 'accessor'], function() use ($app) {
  $app->get('api/v1/current_user', ['uses'=>'Lib\HoneyBase\Controller\AccountController@current_user']);
  $app->post('api/v1/auth', ['uses'=>'Lib\HoneyBase\Controller\AccountController@auth']);
  $app->post('api/v1/signup', ['uses'=>'Lib\HoneyBase\Controller\AccountController@signup']);
  $app->post('api/v1/signin', ['uses'=>'Lib\HoneyBase\Controller\AccountController@signin']);
  $app->post('api/v1/logout', ['uses'=>'Lib\HoneyBase\Controller\AccountController@logout']);

  $app->post('api/v1/uploader', ['uses'=>'Lib\HoneyBase\Controller\UploadController@upload']);
  $app->post('api/v1/mailer', ['uses'=>'Lib\HoneyBase\Controller\MailController@mailer']);

  $app->post('api/v1/db/insert', ['uses'=>'Lib\HoneyBase\Controller\DataBaseController@insert', 'https' => true]);
  $app->post('api/v1/db/update', ['uses'=>'Lib\HoneyBase\Controller\DataBaseController@update', 'https' => true]);
  $app->post('api/v1/db/delete', ['uses'=>'Lib\HoneyBase\Controller\DataBaseController@delete', 'https' => true]);
  $app->get('api/v1/db/select', ['uses'=>'Lib\HoneyBase\Controller\DataBaseController@select', 'https' => true]);

  $app->get('api/v1/db/users/search', ['uses'=>'Lib\HoneyBase\Controller\DataBaseController@ambiguous_select', 'https' => true]);
  $app->get('api/v1/db/first', ['uses'=>'Lib\HoneyBase\Controller\DataBaseController@first', 'https' => true]);
  $app->get('api/v1/db/last', ['uses'=>'Lib\HoneyBase\Controller\DataBaseController@last', 'https' => true]);
});
$app->get('api/v1/email_verify', ['uses'=>'Lib\HoneyBase\Controller\AccountController@email_verify']);
$app->get('api/v1/db/count', 'Lib\HoneyBase\Controller\DataBaseController@count');
