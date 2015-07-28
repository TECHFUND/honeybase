<?php namespace App\Models;

use App\Util\NuLog;
use App\Util\Util;
use App\Models\MysqlAdaptor;

class Skill {

  public static function find($id){
    $db = new MysqlAdaptor();
    $res = $db->select("skills", ["id"=>"$id"]);
    if($res['flag']){
      return $res['data'][0];
    } else {
      return null;
    }
  }

  public static function ambiguous_search($q){
    $db = new MysqlAdaptor();
    $users_res = $db->ambiguous_select("skills", $q);
    if( $users_res['flag'] ){
      return $users_res['data'];
    } else {
      return [];
    }
  }

}
