<?php namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Util\NuLog;
use App\Util\Util;

class BasicAuthenticationMiddleware {

  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  // MiddleWareでinsert, select含めたrequestのuser_idを取得してcurrent_user->idと比較するのはありかも
  // insert, update, delete, selectにaccess: all, none, loginを定義できる
  public function handle($request, Closure $next){

    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $header = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];

    // get paramでpassを要求
    $pass = $request->input("pass");
    if($pass == BASIC_PASS){
      $request->current_user = $current_user;
      return $next($request);
    } else {
      return redirect('/');
    }
  }

}
