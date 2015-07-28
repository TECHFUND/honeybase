<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MysqlAdaptor;
use App\Models\User;
use App\Models\Session;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

use App\Util\CommonFunctions;
use App\Util\Mail;
use App\Util\FB;
use Auth;
use Log;
use Cookie;
use App\Util\NuLog;
use App\Util\Util;


class AccountController extends Controller {

  /* jsからajaxするときvar_dumpしてると落ちてallow_originエラーになるので注意 */
  public function current_user(Request $request)
  {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];
    $data = $request->all();
    $db = new MysqlAdaptor();
    $result = $db->select("sessions", ["session_id"=>$session_id]); // "id"=>にしたい
    $flag = false;
    $user = null;


    if( count($result['data']) == 1 ){
      $session = $result['data'][0];
      $user_id = $session['user_id'];
      $headers += ["Set-Cookie"=>SERVICE_NAME."id=$session_id; path=/"];
      $user_array = $db->select("users", ["id"=>$user_id])['data'];
      if( count($user_array) > 0 ){
        $user = $user_array[0];
        $flag = true;
      }
    } else {
      setcookie(SERVICE_NAME.'id', '', time() - 3600, '/');
    }
    return response(["flag"=>$flag, "user"=>User::sanitize($user)], 200, $headers);
  }

  public function signup(Request $request){
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];
    $data = $request->all();
    $email = $data['email'];
    $password = $data['password'];
    $option = (array)json_decode($data['option']);
    $encrypted_password = md5($password.SALT);
    $verify_code = Util::createRandomString(30);
    $basic_option = [
      'full_name'=>"",
      'email'=>$email,
      'description'=>"",
      'picture'=>"",
      'user_access_token'=>"",
      'social_id'=>"",
      'facebook_link'=>"",
      'banned'=>false,
      'created_at'=>Util::ms(),
      'updated_at'=>Util::ms(),
      'encrypted_password'=>$encrypted_password,
      'salt'=>SALT,
      'email_verify'=>false,
      'email_verify_code'=>$verify_code
    ];
    $params = array_merge($basic_option, $option);

    $user = User::create("", $params);
    if( isset($user) ){
      $session_id = Session::create($user);
      $headers += ["Set-Cookie"=>SERVICE_NAME."id=$session_id; path=/"];
      Mail::verify_mail($email, $verify_code);
      return response(["flag"=>true, "user"=>User::sanitize($user)], 200, $headers); // そのままユーザーデータ返したら危ない
    } else {
      return response(["flag"=>false, "user"=>null, "msg"=>"signup failed"], 403, $headers);
    }
  }

  public function email_verify(Request $request) {
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id, true);
    $updated_at = Util::ms();
    $diff = $updated_at - $current_user['created_at'];
    $ms_24hour = 24*60*60*1000;

    if($diff > $ms_24hour){
      return view("tmp.verify_error", ["msg"=>"This verify code has expired, please signup again."]);
    }

    $data = $request->all();
    $code = $data['code'];


    if($code == $current_user['email_verify_code']){
      $db = new MysqlAdaptor();
      $result = $db->update("users", $current_user['id'], ["email_verify"=>true, "updated_at"=>$updated_at]);
      $headers += ["Set-Cookie"=>SERVICE_NAME."id=$session_id; path=/"];

      return view("api.v1.signup.verify_success", ["current_user"=>$current_user]);
    } else {
      return view("api.v1.signup.verify_error", ["msg"=>"Invalid verify code, please signup again."]);
    }
  }

  public function signin(Request $request){
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $data = $request->all();

    $current_user = User::current_user($session_id);
    if ( isset($current_user) ){
      return response(["flag"=>true, "user"=>$current_user], 200, $headers);
    }
    $email = $data['email'];
    $password = $data['password'];
    $encrypted_password = md5($password.SALT);

    $user_res = User::search(['email'=>$email]);
    $user = null;
    if(count($user_res) > 0){
      $user = $user_res[0];
    }

    if( isset($user) ) {
      if ($user['encrypted_password'] == $encrypted_password) {
        $session_id = Session::create($user);
        $headers += ["Set-Cookie"=>SERVICE_NAME."id=$session_id; path=/"];
        return response(["flag"=>true, "user"=>User::sanitize($user)], 200, $headers);
      } else {
        return response(["flag"=>false, "user"=>null, "msg"=>"email or password are not matched"], 403, $headers);
      }
    } else {
      return response(["flag"=>false, "user"=>null, "msg"=>"email or password are not matched"], 403, $headers);
    }
  }

  public function auth(Request $request)
  {
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];
    $session_id = $request->cookie(SERVICE_NAME.'id');

    $data = $request->all();
    if( property_exists($request, "filtered") ){
      $data = $request->filtered;
    }
    $code = 503;
    $msg = "";
    $provider = $data['provider'];
    $social_id = null;

    $options = $data['option'];
    if( gettype($options) != "object" ){
      $options = (object) json_decode($options);
    }

    $token = $options->user_access_token;
    if($provider == "facebook"){
      $me = FB::api("/me", $token);
      $social_id = $me['id'];

      $options->full_name = $me['name'];
      $options->email = $me['email'];
      $options->description = "";
      $options->picture = "http://graph.facebook.com/$social_id/picture?width=300&height=300";
      $options->social_id = $social_id;
      $options->facebook_link = $me['link'];
      $options->user_access_token = $token;
      $options->banned = false;
      $options->encrypted_password = "not available";
      $options->salt = SALT;
      $options->email_verify = false;
      $options->email_verify_code = "";

      $code = 200;
      $user = $this->searchOrCreateUser($social_id, $options);
      $session_id = $this->createOrUpdateSession($user);
      $headers += ["Set-Cookie"=>SERVICE_NAME."id=$session_id; path=/"];
    }

    return response(["flag"=>true, "user"=>User::sanitize($user)], $code, $headers);
  }

  public function logout(Request $request)
  {
    $db = new MysqlAdaptor();
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $result = $db->select("sessions", ["session_id"=>$session_id]);
    $hit_sessions = $result["data"];
    $flag = false;
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];

    setcookie(SERVICE_NAME.'id', '', time() - 3600, '/');
    if( count($hit_sessions) == 1 ){
      return response(["flag"=>true, "message"=>"logged out"], 200, $headers);
    } else {
      return response(["flag"=>false, "message"=>"You're already logged out"], 403, $headers);
    }
  }




  /****************************
   * OAUTH FUNCTION
   ****************************/
  private function searchOrCreateUser($social_id, $options){
    /* アカウントがまだ存在しなかったら作る。存在したらスルー。 */
    $db = new MysqlAdaptor();
    $existing_user = $db->select("users", ["social_id"=>$social_id]);
    $user = null;
    $isNotExist = (count($existing_user['data']) == 0);
    return ($isNotExist) ? User::create($social_id, $options) : $existing_user["data"][0];
  }

  private function createOrUpdateSession($user){
    /* 既存・新規作成ユーザーIDをランダム文字列と紐づける */
    $db = new MysqlAdaptor();
    if(is_array($user)){
      $res = $db->select("sessions", ["user_id"=>$user["id"]]);
      $old_sessions = $res['data'];
      $isAlreadyExist = (count($old_sessions) > 0);
      return ($isAlreadyExist) ? Session::update($user, $old_sessions[0]) : Session::create($user);
    } else {
      NuLog::error("null arg in createOrUpdateSession", __FILE__, __LINE__);
    }
  }

}
