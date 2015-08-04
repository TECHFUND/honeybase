<?php namespace HoneyBase\Core\Controller;

use HoneyBase\Core\Controller\Controller;
use HoneyBase\Core\Model\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Util\Util\NuLog;

class UtilityController extends Controller {

  /* jsからajaxするときvar_dumpしてると落ちてallow_originエラーになるので注意 */
  public function logger(Request $request, $type)
  {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $user_id = (isset($current_user)) ? (int)$current_user['id'] : -1;

    $data = $request->all();
    $context = ( array_key_exists("context", $data) ) ? $data["context"] : "";
    $path = ( array_key_exists("path", $data) ) ? $data["path"] : "*";

    $input = [
      "context"=>$context,
      "path"=>$path,
      "user_id"=>$user_id
    ];

    $code = 503;
    if ( in_array($type, ["info", "error", "warn"], true) ){
      NuLog::$type($input, $path, "frontend");
      $res = ["flag"=>true];
      $code = 200;
    } else {
      NuLog::error(["context"=>"There may be TYPO in the ajax path of /api/v1/logger/{type}.", "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
      $code = 404;
      $res = ["flag"=>false];
    }

    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    return response($res, $code, $headers);
  }
}
