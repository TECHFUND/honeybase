<?php namespace HoneyBase\Core\Middleware;

use Util\Util\NuLog;
use Util\Util\Util;
use HoneyBase\Core\Model\User;
use Exception;

class AccessorParser {

  public $accessor;

  function __construct () {}

  public function setAccessor($accessor){
    $this->accessor = $accessor;
    return isset($this->accessor);
  }

  public function isDatabase(){
    return true;
  }

  public function climbTableBranch(){
    return true;
  }

  public function climbActionBranch(){
    return true;
  }

  public function climbRoleBranch(){
    return true;
  }

  public function matchParamsToAccessor(){
    return true;
  }

  public function climbPathBranch(){
    return true;
  }

  public function matchRoleToAccessor(){
    return true;
  }

  public function matchProviderToAccessor(){
    return true;
  }
}
