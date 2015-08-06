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
  }

  public function climbTableBranch(){
  }

  public function climbActionBranch(){
  }

  public function climbRoleBranch(){
  }

  public function matchParamsToAccessor(){
  }

  public function climbPathBranch(){
  }

  public function matchRoleToAccessor(){
  }

  public function matchProviderToAccessor(){
  }
}
