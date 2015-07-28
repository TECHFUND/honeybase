<?php namespace App\Models;

use App\Util\NuLog;
use App\Models\User;

class Feed {
  public static function all($join = false){
    $db = new MysqlAdaptor();
    $res = null;
    if($join){
      $res = $db->joined_select("feeds", "user_id", "users", "id", []);
    } else {
      $res = $db->select("feeds", []);
    }
    return $res['data'];
  }

  public static function all_with_user(){
    $feeds = [];
    foreach(self::all(false) as $feed){
      $user = User::search(["id" => $feed["user_id"]])[0];
      unset($user['id']);
      unset($user['created_at']);
      unset($user['updated_at']);
      array_push($feeds, array_merge($feed, $user));
    }
    return $feeds;
  }
}
