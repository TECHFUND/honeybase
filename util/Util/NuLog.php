<?php namespace Util\Util;

use Log;

class NuLog{
  public static function info($x, $file=null, $line=null){
    self::text('info', $x, $file, $line);
  }
  public static function warn($x, $file=null, $line=null){
    self::text('warn', $x, $file, $line);
  }
  public static function error($x, $file=null, $line=null){
    self::text('error', $x, $file, $line);
  }

  private static function text ($type, $x, $file, $line){
    $class = 'Log';
    if($file == null || $line == null){
      $class::$type($x);
    } else {
      $class::$type("\n[PLACE] - ".$file.":".$line."\n".
      "[VAL] - ".json_encode($x)."\n".
      self::drip($file, $line)."\n");
    }
  }

  private static function drip ($path, $l) {
    $lines = file($path);
    $back = 10;
    $range = 15;
    $code = "[CODE]>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>\n";
    for($i=0; $i < $range; $i++){
      $pntr = $l-$back+$i;
      if( 0 < $pntr && $pntr < count($lines)){
        $code .= ($pntr+1).":".$lines[$pntr];
      }
    }
    return $code."<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<[EOF]\n";
  }
}
