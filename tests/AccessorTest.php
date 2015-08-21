<?php

use Util\Util\Util;
use Util\Util\NuLog;
use HoneyBase\Core\Middleware\AccessorParser;

class AccessorTest extends TestCase {

  // DI
  public function testAccessorStructure(){
    // ready
    $accessor = Util::getJSON(__DIR__."/../app/accessor.json");

    // assert
    // basic structure test
    $this->assertTrue( isset($accessor) );
    $this->assertTrue( property_exists($accessor, "register") );
    $this->assertTrue( property_exists($accessor, "database") );

    // register structure test
    foreach($accessor->register as $path => $value){
      $this->assertEquals(0, strpos($path, "/"));
      $this->assertTrue( property_exists($value, "provider") );
      $this->assertTrue( property_exists($value, "role") );
      $this->assertTrue( is_array($value->provider) );
      $this->assertTrue( is_array($value->role) );
    }

    // database structure test
    foreach($accessor->database as $table => $table_value) {
      $this->assertTrue( is_string($table) );
      $this->assertTrue( is_object($table_value) );
      $this->assertTrue( count( (array)$table_value ) > 0 );

      foreach( $table_value as $action => $action_value ){
        var_dump($action);
        $this->assertTrue( in_array($action, ["insert", "update", "select", "delete"]) );
        $this->assertTrue( count( (array)$action_value ) > 0 );
        foreach( $action_value as $role => $params ){
          $this->assertTrue( is_string($role) );
          $this->assertTrue( is_array($params) );
          $this->assertTrue( count($params) > 0 );
        }
      }
    }

  } // str->bool

}

