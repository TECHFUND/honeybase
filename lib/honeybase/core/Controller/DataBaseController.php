<?php namespace HoneyBase\Core\Controller;

use HoneyBase\Core\Controller\Controller;
use Honeybase\Core\Model\MysqlAdaptor;
use Honeybase\Core\Model\Uploader;
use Honeybase\Core\Model\User;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Util\Util\NuLog;

class DataBaseController extends Controller {

  /* jsからajaxするときvar_dumpしてると落ちてallow_originエラーになるので注意 */
  public function insert(Request $request)
  {
    $data = $request->all();
    $tbl = $data["table"];

    $value = json_decode($data['value']);
    if( property_exists($request, "value") ){
      $value = $request->value;
    }

    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    $db = new MysqlAdaptor();

    $result = false;

    if($tbl == "" || $value == null){
      NuLog::error(["context"=>"push input invalid", "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
      $res = ["flag"=>false, "data"=>null];
      return response($res, 200, $headers);
    } else {
      // $value = Uploader::uploadBase64($value);
      $result = $db->insert($tbl, $value);
      $data['id'] = $result['id'];
      $res = ["flag"=>$result['flag'], "data"=>($result['flag']) ? $data : null];
      return response($res, 200, $headers);
    }
  }

  public function update(Request $request)
  {
    $data = $request->all();
    $tbl = $data["table"];
    $id = $data['id'];
    $value = $request->value;

    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    $db = new MysqlAdaptor();
    $flag = false;

    if($tbl == "" || $id < 0){
      NuLog::error(["context"=>"set input invalid", "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
    } else {
      $result = $db->update($tbl, $id, $value);
      $flag = $result["flag"];
    }

    $res = ["flag"=>$flag, "data"=> ["id"=>$id, "value"=>$value]];
    return response($res, 200, $headers);
  }

  public function delete(Request $request)
  {
    $data = $request->all();
    $tbl = $data["table"];
    $id = $data['id'];

    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    $db = new MysqlAdaptor();
    $result = false;

    if($tbl == "" || $id < 0){
      NuLog::error(["context"=>"remove input invalid", "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
    } else {
      $result = $db->delete($tbl, $id)["flag"];
    }
    $res = ["flag"=>$result, "id"=>$id];
    return response($res, 200, $headers);
  }

  public function select(Request $request)
  {
    $data = $request->all();
    $tbl = $data["table"];
    $value = json_decode($data['value']);
    if( property_exists($request, "value") ){
      $value = $request->value;
    }

    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    $db = new MysqlAdaptor();
    $result = false;

    if($tbl == "" || $value == null){
      NuLog::error(["context"=>"select input invalid", "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
    } else {
      $result = $db->select($tbl, $value);
    }
    $res = $result;
    return response($res, 200, $headers);
  }


  /*
  * UTIL FUNCTIONS
  */
  public function ambiguous_select(Request $request){
    $data = $request->all();
    $value = $request->value;

    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    $result = false;

    if($value == null){
      return response(["flag"=>false, "data"=>[]], 403, $headers);
    } else {
      $users = User::ambiguous_search($value);
      return response(["flag"=>true, "data"=>$users], 200, $headers);
    }
  }

  public function count(Request $request) {
    $data = $request->all();
    $tbl = $data["table"];
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];
    $db = new MysqlAdaptor();
    $result = false;

    $query = null;
    if( array_key_exists("query", $data)){
      $query = json_decode($data["query"]);
    }

    $_query = [];
    foreach ($query as $tuple){
      $_query = array_merge($_query, [$tuple[0]=>$tuple[1]]);
    }

    if($tbl == "" ){//|| $value == null){
      NuLog::error(["context"=>"select input invalid", "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
    } else if(count($_query) == 0) {
      $result = $db->count($tbl, []);
    } else {
      $result = $db->count($tbl, $_query);
    }
    $res = $result;
    return response($res, 200, $headers);
  }

  public function first(Request $request) {
    $data = $request->all();
    $tbl = $data["table"];
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    $db = new MysqlAdaptor();
    $result = false;

    if($tbl == "" ){//|| $value == null){
      NuLog::error(["context"=>"select input invalid", "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
    } else {
      $result = $db->first($tbl, []);
    }
    $res = $result;
    return response($res, 200, $headers);
  }
  public function last(Request $request) {
    $data = $request->all();
    $tbl = $data["table"];
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    $db = new MysqlAdaptor();
    $result = false;

    if($tbl == "" ){//|| $value == null){
      NuLog::error(["context"=>"select input invalid", "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
    } else {
      $result = $db->last($tbl, []);
    }
    $res = $result;
    return response($res, 200, $headers);
  }
}
