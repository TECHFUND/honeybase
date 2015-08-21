<?php

use Util\Util\Util;
use Util\Util\NuLog;
use HoneyBase\Core\Middleware\AccessorParser;

class AccessorParserAbnormalSystemTest extends TestCase {

  // DI
  public function testSetAccessor(){
    // ready
    $accessor = Util::getJSON(__DIR__."/accessor_abnormal.json");
    $parser = new AccessorParser();

    // eval
    $result = $parser->setAccessor($accessor);

    // assert
    $this->assertTrue($result);
    $this->assertTrue(isset($parser->accessor));
    $this->assertEquals("object", gettype($parser->accessor));
    $this->assertTrue(isset($parser->accessor->database));
    $this->assertTrue(isset($parser->accessor->register));
  } // str->bool


  /*
  * Database context
  */
  // Set keys to matcher
  // database
  public function testClimbTableBranch(){
    // ready
    $parser = $this->parser();

    // eval
    $result = $parser->climbTableBranch("users");

    // assert
    $this->assertFalse($result);
    $this->assertEquals("NULL", gettype($parser->table));
    $this->assertEmpty($parser->table);
  } // str->bool
  public function testClimbActionBranch(){
    // ready
    $parser = $this->parser();
    $result = $parser->climbTableBranch("users");

    // eval
    $result = $parser->climbActionBranch("insert");

    // assert
    $this->assertFalse($result);
    $this->assertEquals("NULL", gettype($parser->action));
    $this->assertEmpty($parser->action);
  } // str->bool
  public function testClimbRoleBranch(){ // And get params
    // ready
    $parser = $this->parser();
    $result = $parser->climbTableBranch("users");
    $result = $parser->climbActionBranch("insert");

    // eval
    $result = $parser->climbRoleBranch("client");

    // assert
    $this->assertFalse($result);
    $this->assertEquals("NULL", gettype($parser->params));
    $this->assertEmpty($parser->params);
  } // str->bool // current_userのrole

  // Set matcher
  // In the database context, params input in the HTTP Request are matcher
  public function testMatchParamsToAccessor(){
    // ready
    $parser = $this->parser();
    $result = $parser->climbTableBranch("users");
    $result = $parser->climbActionBranch("insert");
    $result = $parser->climbRoleBranch("client");

    // eval
    $result = $parser->matchParamsToAccessor(["body", "created_at", "updated_at"]);

    // assert
    $this->assertTrue($result);
  } // array -> bool



  /*
  * Register context
  */
  // Set keys to matcher
  public function testClimbPathBranch(){
    // ready
    $parser = $this->parser();

    // eval
    $result = $parser->climbPathBranch("/");

    // assert
    $this->assertTrue($result);
    $this->assertTrue(isset($parser->provider));
    $this->assertEquals("array", gettype($parser->provider));
    $this->assertTrue( count((array)$parser->provider) > 0 );

    $this->assertTrue(isset($parser->role));
    $this->assertEquals("array", gettype($parser->role));
    $this->assertTrue( count((array)$parser->role) > 0 );
  } // str->bool


  // Set matcher
  // In the register context, provider&role inputs in the HTTP Request are matchers
  public function testMatchRoleToAccessor(){
    // ready
    $parser = $this->parser();

    // eval
    $result = $parser->matchRoleToAccessor("admin");

    // assert
    $this->assertTrue($result);
  } // str->bool

  public function testMatchProviderToAccessor(){
    // ready
    $parser = $this->parser();

    // eval
    $result = $parser->matchProviderToAccessor("facebook");

    // assert
    $this->assertTrue($result);
  } // str->bool





  // before
  private function parser(){
    $accessor = Util::getJSON(__DIR__."/accessor_abnormal.json");
    $parser = new AccessorParser();
    $result = $parser->setAccessor($accessor);
    return $parser;
  }

  private function request(){
    $input = function ($name){
      if( isset($name) ) {
        return (object)[];
      } else {
        return null;
      }
    };
    return (object)["input" => $input];
  }
}
