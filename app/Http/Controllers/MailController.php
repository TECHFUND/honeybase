<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MysqlAdaptor;
use App\Util\Mail;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

use Log;
use Cookie;
use App\Util\NuLog;
use App\Util\Util;


class MailController extends Controller {

  /* jsからajaxするときvar_dumpしてると落ちてallow_originエラーになるので注意 */
  public function mailer(Request $request)
  {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    $data = $request->all();
    $mail_name = $data["mail_name"];
    $params = json_decode($data['value']);
    $res = Mail::send($mail_name, $params);
    if ($res) {
      return response(["flag"=>true], 200, $headers);
    } else {
      return response(["flag"=>false], 403, $headers);
    }
  }
}
