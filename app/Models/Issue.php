<?php namespace App\Models;

use App\Util\NuLog;
use App\Models\User;

class Issue {
  public static function all($join = false){

    $db = new MysqlAdaptor();
    $issues = [];
    if($join){
      $res = $db->joined_select("issues", "user_id", "users", "id", []); //issues.user_idとusers.idでjoinして全件取得
      $issues = $res['data'];
    } else {
      $res = $db->select("issues", []);
      $issues = $res['data'];
    }
    return $issues;
  }

  public static function find($id){
    $db = new MysqlAdaptor();
    $res = $db->select("issues", ["id"=>$id]);
    if($res['flag']){
      return $res['data'][0];
    } else {
      return null;
    }
  }

  public static function select($q){
    $db = new MysqlAdaptor();
    $res = $db->select("issues", $q);
    if($res['flag']){
      return $res['data'];
    } else {
      return [];
    }
  }

  public static function all_with_user(){
    $issues = [];
    foreach(self::all(false) as $issue){
      $user = User::search(["id" => $issue["user_id"]])[0];
      unset($user['id']);
      array_push($issues, array_merge($issue, $user));
    }
    return $issues;
  }

  public static function executors($issue_id){
    $db = new MysqlAdaptor();
    $res1 = $db->select("issue_executors", ["issue_id"=>$issue_id]);
    if($res1['flag']){
      $issue_executors = $res1['data'];
      $executors = [];
      foreach($issue_executors as $issue_executor){
        array_push($executors, User::find($issue_executor['executor_id']));
      }
      return $executors;
    } else {
      return [];
    }
  }

  public static function experts($issue_id){
    $db = new MysqlAdaptor();
    $res1 = $db->select("issue_experts", ["issue_id"=>$issue_id]);
    if($res1['flag']){
      $issue_experts = $res1['data'];
      $experts = [];
      foreach($issue_experts as $issue_expert){
        array_push($experts, User::find($issue_expert['expert_id']));
      }
      return $experts;
    } else {
      return [];
    }
  }

  public static function client($issue_id){
    $issue = self::find($issue_id);
    return User::find($issue['user_id']);
  }

  public static function isClient($issue_id, $current_user){
    return (self::find($issue_id)['user_id'] === $current_user['id']);
  }

  public static function isExecutor($issue_id, $current_user){
    $db = new MysqlAdaptor();
    $res1 = $db->select("issue_executors", ["issue_id"=>$issue_id]);
    if($res1['flag']){
      $issue_executors = $res1['data'];
      $ids = [];
      foreach($issue_executors as $issue_executor){
        array_push($ids, $issue_executor['executor_id']);
      }
      return in_array($current_user['id'], $ids, true);
    } else {
      return false;
    }

  }


  public static function not_enrolled_experts($issue_id){
    $db = new MysqlAdaptor();
    $res1 = $db->select("issue_experts", ["issue_id"=>$issue_id]);
    $issue_experts = $res1['data'];

    $sql = "SELECT * FROM users where type = 'expert' ";
    foreach($issue_experts as $issue_expert){
      $sql .= 'AND ';
      $sql .= 'id != '.$issue_expert['expert_id'].' ';
    }

    $res2 = $db->sql($sql);

    return $res2['data'];
  }


}
