<?php namespace App\Models;

use App\Util\NuLog;
use App\Util\Util;
use App\Util\CommonFunctions;
use App\Models\MysqlAdaptor;

class Expert {
  public static function find($user_id){
    $expert = null;
    $db = new MysqlAdaptor();
    $sql = "SELECT u.*, e.company, e.position, e.verify FROM users AS u LEFT JOIN experts AS e ON u.id = e.user_id WHERE u.id = ".$user_id;
    $user_array = $db->sql($sql)['data'];
    if( count($user_array) > 0 ){
      $user = $user_array[0];
    }
    return $user;
  }
}
