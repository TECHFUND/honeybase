<?php namespace Lib\HoneyBase\Model;

use Util\Util\NuLog;
use Util\Util\Util;
use Lib\HoneyBase\Model\MysqlAdaptor;

class Session {
  public static function create($user){
    $new_session_id = Util::createRandomString(100);
    $data = [
      "session_id"=>$new_session_id,
      "user_id"=>$user['id'],
      "created_at"=>Util::ms(),
      "updated_at"=>Util::ms()
    ];
    $db = new MysqlAdaptor();
    $db->insert("sessions", $data);
    return $new_session_id;
  }
  public static function update($user, $old_session){
    $new_session_id = Util::createRandomString(100);
    $target_id = $old_session['id'];
    $data = [
      "session_id"=>$new_session_id,
      "user_id"=>$user['id'],
      "updated_at"=>Util::ms()
    ];
    $db = new MysqlAdaptor();
    $db->update("sessions", $target_id, $data);
    return $new_session_id;
  }

  public static function delete($id){
    $db = new MysqlAdaptor();
    $db->delete("sessions", $id);
    setcookie(SERVICE_NAME.'id', '', time() - 3600, '/');
  }
}
