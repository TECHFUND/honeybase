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
    $user_id = (array_key_exists("id", $current_user)) ? $current_user['id'] : "anonymous";

    $data = $request->all();
    $context = ( array_key_exists("context", $data) ) ? $data["context"] : "";
    $path = ( array_key_exists("path", $data) ) ? $data["path"] : "*";
    $msg = ( array_key_exists("msg", $data) ) ? $data["msg"] : "";
    $file = ( array_key_exists("file", $data) ) ? $data["file"] : "*";
    $line = ( array_key_exists("line", $data) ) ? $data["line"] : "*";
    $action = ( array_key_exists("action", $data) ) ? $data["action"] : "*";
    $selector = ( array_key_exists("selector", $data) ) ? $data["selector"] : "*";
    $val = ( array_key_exists("val", $data) ) ? $data["val"] : "";

    $str = '{
      "context": '.$context.',
      "path": '.$path.',
      "msg": '.$msg.',
      "action": '.$action.',
      "selector": '.$selector.',
      "val": '.$val.',
      "user_id": '.$user_id.'
    }';

    $code = 503;
    if ( in_array($type, ["info", "error", "warn"], true) ){
      NuLog::$type($str, $file, $line);
      $res = ["flag"=>true];
      $code = 200;
    } else {
      NuLog::error("There may be TYPO in the ajax path of /api/v1/logger/{type}.", __FILE__, __LINE__);
      $code = 404;
      $res = ["flag"=>false];
    }

    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    return response($res, $code, $headers);
  }
}
