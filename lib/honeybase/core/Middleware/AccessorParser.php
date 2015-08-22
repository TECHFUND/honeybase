<?php namespace HoneyBase\Core\Middleware;

use Util\Util\NuLog;
use Util\Util\Util;
use HoneyBase\Core\Model\User;
use Exception;

class AccessorParser {

  public $accessor;
  public $current_user;

  public $table;
  public $action;
  public $params;

  public $provider;
  public $role;


  function __construct () {}

  public function setAccessor($accessor){
    $this->accessor = $accessor;
    return isset($this->accessor);
  }

  public function setCurrentUser($current_user){
    $this->current_user = $current_user;
    return isset($this->current_user);
  }

  public function climbTableBranch($table_name){
    $bool = false;
    try {
      $database = $this->accessor->database;
      $this->table = $database->$table_name;
      $bool = isset($this->table);
    } catch (Exception $e) {
      NuLog::error(["context"=>$e->getMessage().". Key:".$table_name, "user_id"=>(isset($this->current_user)) ? $this->current_user['id'] : -1], __FILE__, __LINE__);
    }
    return $bool;
  }

  public function climbActionBranch($action_name){
    $bool = false;
    try {
      $this->action = $this->table->$action_name;
      $bool = isset($this->action);
    } catch (Exception $e) {
      NuLog::error(["context"=>$e->getMessage().". Key:".$action_name, "user_id"=>(isset($this->current_user)) ? $this->current_user['id'] : -1], __FILE__, __LINE__);
    }
    return $bool;
  }

  public function climbRoleBranch($role_name){
    $bool = false;
    try {
      $this->params = $this->action->$role_name;
      $bool = isset($this->params);
    } catch (Exception $e) {
      NuLog::error(["context"=>$e->getMessage().". Key:".$role_name, "user_id"=>(isset($this->current_user)) ? $this->current_user['id'] : -1], __FILE__, __LINE__);
    }
    return $bool;
  }

  public function matchParamsToAccessor($params_input){
    $res = $this->params == $params_input; // parse要る
    return true;
  }

  public function climbPathBranch($path_name){
    $bool = false;
    try {
      $register = $this->accessor->register;
      $path_obj = $register->$path_name;
      $this->provider = $path_obj->provider;
      $this->role = $path_obj->role;
      $bool = isset($this->provider) && isset($this->role);
    } catch (Exception $e) {
      NuLog::error(["context"=>$e->getMessage().". Key:".$path_name, "user_id"=>(isset($this->current_user)) ? $this->current_user['id'] : -1], __FILE__, __LINE__);
    }
    return $bool;
  }

  public function matchRoleToAccessor(){
    return true;
  }

  public function matchProviderToAccessor(){
    return true;
  }
}
