<?php

use App\Util\NuLog;
use App\Models\User;
use App\Models\Team;
use App\Http\Controllers\Controller;
use App\Models\MysqlAdaptor;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is issueed.
|
*/

/* v1 */

/* for all */
$app->group(['middleware' => 'profile_filling'], function() use ($app) {
  $app->get('/', ['uses'=>'App\Http\Controllers\V1Controller@index', 'https' => true]);

  $app->get('/about', ['uses'=>'App\Http\Controllers\V1Controller@about', 'https' => true]);

  // admin, expert, client, 皆で使用する
  $app->get('/issues/{issue_id}/discussion', ['uses'=>'App\Http\Controllers\V1Controller@discussion', 'https' => true]);
  $app->get('/issues/{issue_id}/to_offline', ['uses'=>'App\Http\Controllers\V1Controller@to_offline', 'https' => true]);

  /* for expert */
  $app->group(['middleware' => 'expert'], function() use ($app) {
    $app->get('/expert/issues', ['uses'=>'App\Http\Controllers\V1Controller@expert_issues', 'https' => true]); //chat
    $app->get('/expert/new', ['uses'=>'App\Http\Controllers\V1Controller@new_expert', 'https' => true]);
    $app->get('/expert/waiting', ['uses'=>'App\Http\Controllers\V1Controller@waiting_expert', 'https' => true]);
  });

  /* for client */
  $app->get('/client/issues/new', ['uses'=>'App\Http\Controllers\V1Controller@new_issue', 'https' => true]);
  $app->group(['middleware' => 'client'], function() use ($app) {
    $app->get('/client/issues', ['uses'=>'App\Http\Controllers\V1Controller@client_issues', 'https' => true]); //chat
    $app->get('/client/issues/{issue_id}', ['middleware' => 'issue_owner', 'uses'=>'App\Http\Controllers\V1Controller@client_issue', 'https' => true]);
  });

  /* for admin */
  $app->get('/admin/login', ['uses'=>'App\Http\Controllers\V1Controller@admin_login', 'https' => true]);
  $app->group(['middleware' => 'admin'], function() use ($app) {
    $app->get('/admin', ['uses'=>'App\Http\Controllers\V1Controller@admin_issues', 'https' => true]);
    $app->get('/admin/issues/{issue_id}', ['uses'=>'App\Http\Controllers\V1Controller@admin_issue', 'https' => true]);
    $app->get('/admin/issues/{issue_id}/discussion', ['uses'=>'App\Http\Controllers\V1Controller@admin_discussion', 'https' => true]);
    $app->get('/admin/analytics', ['uses'=>'App\Http\Controllers\V1Controller@analytics', 'https' => true]);
    $app->get('/admin/send_mail', ['middleware'=>'admin', 'uses'=>'App\Http\Controllers\AdminController@send_mail', 'https' => true]);
    $app->get('/admin/users', ['middleware'=>'admin', 'uses'=>'App\Http\Controllers\AdminController@users', 'https' => true]);
  });
});





/* v2 */

/*
* 非ログイン時のview
*/
$app->group(['prefix' => 'v2'], function ($app) {
  $app->get('/', ['uses'=>'App\Http\Controllers\TemporalController@index', 'https' => true]);
  $app->get('/client_login', ['uses'=>'App\Http\Controllers\TemporalController@client_login', 'https' => true]);
  $app->get('/expert_login', ['uses'=>'App\Http\Controllers\TemporalController@expert_login', 'https' => true]);
  $app->get('/issues/{issue_id}', ['uses'=>'App\Http\Controllers\IssueController@show', 'https' => true]);

  /*
  * login
  */
  $app->get('/users/{user_id}', ['uses'=>'App\Http\Controllers\TemporalController@user', 'https' => true]);
  $app->get('/issues/{issue_id}/edit', ['middleware'=>'client', 'uses'=>'App\Http\Controllers\IssueController@edit', 'https' => true]);

  /*
  * client
  */
  $app->get('/client/issues', ['middleware'=>'client', 'uses'=>'App\Http\Controllers\ClientController@issues', 'https' => true]); //chat
  $app->get('/client/issues/{issue_id}/chat', ['middleware'=>'client', 'uses'=>'App\Http\Controllers\ClientController@chat', 'https' => true]);
  $app->get('/client/issues/{issue_id}/group_chat', ['middleware'=>'client', 'uses'=>'App\Http\Controllers\ClientController@group_chat', 'https' => true]);
  $app->get('/client/edit', ['middleware'=>'client', 'uses'=>'App\Http\Controllers\ClientController@edit', 'https' => true]); //chat

  /*
  * expert
  */
  $app->get('/expert/issues', ['middleware'=>'expert', 'uses'=>'App\Http\Controllers\ExpertController@issues', 'https' => true]); //chat
  $app->get('/expert/issues/{issue_id}', ['middleware'=>'expert', 'uses'=>'App\Http\Controllers\ExpertController@chat', 'https' => true]);
  $app->get('/expert/issues/{issue_id}/discussion', ['middleware'=>'expert', 'uses'=>'App\Http\Controllers\ExpertController@group_chat', 'https' => true]);
  $app->get('/expert/edit', ['middleware'=>'expert', 'uses'=>'App\Http\Controllers\ExpertController@edit', 'https' => true]); //chat

  /*
  * admin
  */
  $app->get('/admin', ['uses'=>'App\Http\Controllers\AdminController@index', 'https' => true]);
  $app->get('/admin/issues', ['middleware'=>'admin', 'uses'=>'App\Http\Controllers\AdminController@issues', 'https' => true]);
  $app->get('/admin/issues/{issue_id}/experts', ['middleware'=>'admin', 'uses'=>'App\Http\Controllers\AdminController@issue_experts', 'https' => true]);
  $app->get('/admin/issues/{issue_id}/experts/{expert_id}/send_mail', ['middleware'=>'admin', 'uses'=>'App\Http\Controllers\AdminController@send_mail', 'https' => true]);
  $app->get('/admin/send_mail', ['middleware'=>'admin', 'uses'=>'App\Http\Controllers\AdminController@send_mail', 'https' => true]);
  $app->get('/admin/chats', ['middleware'=>'admin', 'uses'=>'App\Http\Controllers\AdminController@chats', 'https' => true]);
  $app->get('/admin/users', ['middleware'=>'admin', 'uses'=>'App\Http\Controllers\AdminController@users', 'https' => true]);

  /*
  * original API
  */
  $app->get('/expert/skills/search', ['middleware'=>'expert', 'uses'=>'App\Http\Controllers\ExpertController@search', 'https' => true]); //chat
});


/**************************
* HONEYBASE
**************************/
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
