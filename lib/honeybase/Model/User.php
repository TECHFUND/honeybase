<?php namespace Lib\HoneyBase\Model;

use Util\Util\NuLog;
use Util\Util\Util;
use Util\Util\CommonFunctions;
use Lib\HoneyBase\Model\MysqlAdaptor;

class User {
  public static function all(){
    $db = new MysqlAdaptor();
    $res = $db->select("users", []);
    return $res['data'];
  }

  public static function current_user($session_id, $email_verification_flag=false){
    $current_user = null;
    $db = new MysqlAdaptor();
    $session_array = $db->select("sessions", ["session_id"=>$session_id])['data'];
    if( count($session_array) > 0 ){
      $session = $session_array[0];
      $current_user_array = $db->select("users", ["id"=>$session['user_id']])['data'];
      if( count($current_user_array) > 0 ){
        $current_user = $current_user_array[0];
      }
    }
    if($email_verification_flag){
      return self::sanitize_for_email_verification($current_user);
    } else {
      return self::sanitize($current_user);
    }
  }

  public static function create($social_id, $options){
    $user_data = ["social_id"=>$social_id, "type"=>"", "created_at"=>Util::ms(), "updated_at"=>Util::ms()];
    $user_data = array_merge($user_data, (array)$options);
    $db = new MysqlAdaptor();
    $inserted_result = $db->insert("users", $user_data);
    $user_data += ["id"=>$inserted_result['id']];
    $user = ($inserted_result['flag']) ? $user_data : null;
    return $user;
  }


  public static function find($id){
    $user = null;
    $db = new MysqlAdaptor();
    $user_array = $db->select("users", ["id"=>$id])['data'];
    if( count($user_array) > 0 ){
      $user = $user_array[0];
    }
    return $user;
  }

  public static function search($q){
    $users = [];
    $db = new MysqlAdaptor();
    $users_res = $db->select("users", $q);
    if( $users_res['flag'] ){
      return $users_res['data'];
    } else {
      return [];
    }
  }

  public static function ambiguous_search($q){
    $users = [];
    $db = new MysqlAdaptor();
    $users_res = $db->ambiguous_select("users", $q);
    if( $users_res['flag'] ){
      return $users_res['data'];
    } else {
      return [];
    }
  }

  public static function sanitize($user){
    unset($user['email_verify_code']);
    unset($user['encrypted_password']);
    unset($user['salt']);
    unset($user['social_id']);
    unset($user['user_access_token']);
    return $user;
  }

  public static function sanitize_for_email_verification($user){
    unset($user['encrypted_password']);
    unset($user['salt']);
    unset($user['social_id']);
    unset($user['user_access_token']);
    return $user;
  }

}
