<?php namespace App\Models;

use App\Util\NuLog;
use App\Models\User;

class Admin {
  public static function all(){
    $db = new MysqlAdaptor();
    $res = $db->select("users", ["type"=>"admin"]);
    return $res['data'];
  }
}
