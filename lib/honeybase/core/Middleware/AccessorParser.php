<?php namespace HoneyBase\Core\Middleware;

use Util\Util\NuLog;
use Util\Util\Util;
use HoneyBase\Core\Model\User;
use Exception;

class AccessorParser {

  public $accessor;

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

  public function climbTableBranch($table_name){
    $database = $this->accessor->database;
    $this->table = $database->$table_name;
    return true;
  }

  public function climbActionBranch($action_name){
    $this->action = $this->table->$action_name;
    return true;
  }

  public function climbRoleBranch($role_name){
    $this->params = $this->action->$role_name;
    return true;
  }

  public function matchParamsToAccessor($params_input){
    $res = $this->params == $params_input; // parse要る
    return true;
  }

  public function climbPathBranch($path_name){
    $register = $this->accessor->register;
    $path_obj = $register->$path_name;
    $this->provider = $path_obj->provider;
    $this->role = $path_obj->role;
    return true;
  }

  public function matchRoleToAccessor(){
    return true;
  }

  public function matchProviderToAccessor(){
    return true;
  }
}
