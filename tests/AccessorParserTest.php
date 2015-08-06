<?php

use Util\Util\Util;
use Util\Util\NuLog;
use HoneyBase\Core\Middleware\AccessorParser;

class AccessorParserTest extends TestCase {

  // before
  private function parser(){
    $accessor = Util::getJSON(__DIR__."/accessor.json");
    $parser = new AccessorParser();
    $result = $parser->setAccessor($accessor);
    return $parser;
  }

  // DI
  public function testSetAccessor(){
    $accessor = Util::getJSON(__DIR__."/accessor.json");
    $parser = new AccessorParser();
    $result = $parser->setAccessor($accessor);
    $this->assertTrue($result);
  } // str->bool


  /*
  * Database context
  */
  // Existance of "table input" effect to "register" or "database"
  public function testIsDatabase(){
    $parser = $this->parser();
    $result = $parser->isDatabase("issues");
    $this->assertTrue($result);
  } // str->bool

  // Set keys to matcher
  // database
  public function testClimbTableBranch(){
    $parser = $this->parser();
    $result = $parser->climbTableBranch("issues");
    $this->assertTrue($result);
  } // str->bool
  public function testClimbActionBranch(){
    $parser = $this->parser();
    $result = $parser->climbActionBranch("insert");
    $this->assertTrue($result);
  } // str->bool
  public function testClimbRoleBranch(){
    $parser = $this->parser();
    $result = $parser->climbRoleBranch("admin");
    $this->assertTrue($result);
  } // str->bool // current_userのrole

  // Set matcher
  // In the database context, params input in the HTTP Request are matcher
  public function testMatchParamsToAccessor(){
    $parser = $this->parser();
    $result = $parser->matchParamsToAccessor(["body", "created_at", "updated_at"]);
    $this->assertTrue($result);
  } // array -> bool



  /*
  * Register context
  */
  // Set keys to matcher
  public function testClimbPathBranch(){
    $parser = $this->parser();
    $result = $parser->climbPathBranch("/");
    $this->assertTrue($result);
  } // str->bool

  // Set matcher
  // In the register context, provider&role inputs in the HTTP Request are matchers
  public function testMatchRoleToAccessor(){
    $parser = $this->parser();
    $result = $parser->matchRoleToAccessor("admin");
    $this->assertTrue($result);
  } // str->bool
  public function testMatchProviderToAccessor(){
    $parser = $this->parser();
    $result = $parser->matchProviderToAccessor("facebook");
    $this->assertTrue($result);
  } // str->bool
}
