<?php

$app->routeMiddleware([
  'accessor' => 'HoneyBase\Core\Middleware\AccessorMiddleware'
]);

$app->group(['middleware' => 'accessor'], function() use ($app) {
  $app->get('api/v1/current_user', ['uses'=>'HoneyBase\Core\Controller\AccountController@current_user']);
  $app->post('api/v1/auth', ['uses'=>'HoneyBase\Core\Controller\AccountController@auth']);
  $app->post('api/v1/signup', ['uses'=>'HoneyBase\Core\Controller\AccountController@signup']);
  $app->post('api/v1/signin', ['uses'=>'HoneyBase\Core\Controller\AccountController@signin']);
  $app->post('api/v1/logout', ['uses'=>'HoneyBase\Core\Controller\AccountController@logout']);

  $app->post('api/v1/uploader', ['uses'=>'HoneyBase\Core\Controller\UploadController@upload']);
  $app->post('api/v1/mailer', ['uses'=>'HoneyBase\Core\Controller\MailController@mailer']);

  $app->post('api/v1/db/insert', ['uses'=>'HoneyBase\Core\Controller\DataBaseController@insert', 'https' => true]);
  $app->post('api/v1/db/update', ['uses'=>'HoneyBase\Core\Controller\DataBaseController@update', 'https' => true]);
  $app->post('api/v1/db/delete', ['uses'=>'HoneyBase\Core\Controller\DataBaseController@delete', 'https' => true]);
  $app->get('api/v1/db/select', ['uses'=>'HoneyBase\Core\Controller\DataBaseController@select', 'https' => true]);

  $app->get('api/v1/db/users/search', ['uses'=>'HoneyBase\Core\Controller\DataBaseController@ambiguous_select', 'https' => true]);
  $app->get('api/v1/db/first', ['uses'=>'HoneyBase\Core\Controller\DataBaseController@first', 'https' => true]);
  $app->get('api/v1/db/last', ['uses'=>'HoneyBase\Core\Controller\DataBaseController@last', 'https' => true]);
});
$app->get('api/v1/email_verify', ['uses'=>'HoneyBase\Core\Controller\AccountController@email_verify']);
$app->get('api/v1/db/count', 'HoneyBase\Core\Controller\DataBaseController@count');
