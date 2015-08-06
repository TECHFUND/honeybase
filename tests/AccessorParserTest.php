<?php

use Util\Util\Util;
use HoneyBase\Core\Middleware\AccessorParser;

class AccessorParserTest extends TestCase {

  // DI
  public function testSetAccessor(){
    $accessor = Util::getJSON("accessor.json");
    $parser = new AccessorParser();
    $result = $parser->setAccessor($accessor);
    $this->assertTrue($result);

    $this->parser = $this->parser;
  } // str->bool


  /*
  * Database context
  */
  // Existance of "table input" effect to "register" or "database"
  public function testIsDatabase(){
    $result = $this->parser->isDatabase("issues");
    $this->assertTrue($result);

    $this->parser = $this->parser;
  } // str->bool

  // Set keys to matcher
  // database
  public function testClimbTableBranch(){
    $result = $this->parser->climbTableBranch("issues");
    $this->assertTrue($result);

    $this->parser = $this->parser;
  } // str->bool
  public function testClimbActionBranch(){
    $result = $this->parser->climbActionBranch("insert");
    $this->assertTrue($result);

    $this->parser = $this->parser;
  } // str->bool
  public function testClimbRoleBranch(){
    $result = $this->parser->climbRoleBranch("admin");
    $this->assertTrue($result);

    $this->parser = $this->parser;
  } // str->bool // current_userのrole

  // Set matcher
  // In the database context, params input in the HTTP Request are matcher
  public function testMatchParamsToAccessor(){
    $result = $this->parser->matchParamsToAccessor(["body", "created_at", "updated_at"]);
    $this->assertTrue($result);

    $this->parser = $this->parser;
  } // array -> bool



  /*
  * Register context
  */
  // Set keys to matcher
  public function testClimbPathBranch(){
    $result = $this->parser->climbPathBranch("/");
    $this->assertTrue($result);

    $this->parser = $this->parser;
  } // str->bool

  // Set matcher
  // In the register context, provider&role inputs in the HTTP Request are matchers
  public function testMatchRoleToAccessor(){
    $result = $this->parser->matchRoleToAccessor("admin");
    $this->assertTrue($result);

    $this->parser = $this->parser;
  } // str->bool
  public function testMatchProviderToAccessor(){
    $result = $this->parser->matchProviderToAccessor("facebook");
    $this->assertTrue($result);

    $this->parser = $this->parser;
  } // str->bool
}
