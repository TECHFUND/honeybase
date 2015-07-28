<?php namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Expert;
use App\Util\NuLog;
use App\Util\Util;

class ProfileFillingMiddleware {

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

    // ログインしてたら続ける
    if($current_user != null){

      $isProfileFilledExpert = $current_user['type'] == "expert" && Expert::find($current_user['id'])["verify"];
      $isAdmin = $current_user['type'] == "admin";
      $isClient = $current_user['type'] == "client";

      if( $isProfileFilledExpert || $isClient ||  $isAdmin ){
        $request->current_user = $current_user;
        return $next($request);
      } else {
        return redirect('/expert/new');
      }
    } else {
      // ログインしてなかったら通す
      return $next($request);
    }
  }

}
