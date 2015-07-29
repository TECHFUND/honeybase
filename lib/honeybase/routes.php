<?php

$app->group(['middleware' => 'accessor'], function() use ($app) {
  $app->get('api/v1/current_user', ['uses'=>'App\Http\Controllers\AccountController@current_user']);
  $app->post('api/v1/auth', ['uses'=>'App\Http\Controllers\AccountController@auth']);
  $app->post('api/v1/signup', ['uses'=>'App\Http\Controllers\AccountController@signup']);
  $app->post('api/v1/signin', ['uses'=>'App\Http\Controllers\AccountController@signin']);
  $app->post('api/v1/logout', ['uses'=>'App\Http\Controllers\AccountController@logout']);

  $app->post('api/v1/uploader', ['uses'=>'App\Http\Controllers\UploadController@upload']);
  $app->post('api/v1/mailer', ['uses'=>'App\Http\Controllers\MailController@mailer']);

  $app->post('api/v1/db/insert', ['uses'=>'App\Http\Controllers\DataBaseController@insert', 'https' => true]);
  $app->post('api/v1/db/update', ['uses'=>'App\Http\Controllers\DataBaseController@update', 'https' => true]);
  $app->post('api/v1/db/delete', ['uses'=>'App\Http\Controllers\DataBaseController@delete', 'https' => true]);
  $app->get('api/v1/db/select', ['uses'=>'App\Http\Controllers\DataBaseController@select', 'https' => true]);

  $app->get('api/v1/db/users/search', ['uses'=>'App\Http\Controllers\DataBaseController@ambiguous_select', 'https' => true]);
  $app->get('api/v1/db/first', ['uses'=>'App\Http\Controllers\DataBaseController@first', 'https' => true]);
  $app->get('api/v1/db/last', ['uses'=>'App\Http\Controllers\DataBaseController@last', 'https' => true]);
});
$app->get('api/v1/email_verify', ['uses'=>'App\Http\Controllers\AccountController@email_verify']);
$app->get('api/v1/db/count', 'App\Http\Controllers\DataBaseController@count');
