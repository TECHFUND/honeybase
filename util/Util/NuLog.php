<?php namespace Util\Util;

use Log;

class NuLog{
  public static function info($x, $file="*", $line="*", $in_detail=false){
    self::text('info', json_encode($x), $file, $line, $in_detail);
  }
  public static function warn($x, $file="*", $line="*", $in_detail=false){
    self::text('warn', json_encode($x), $file, $line, $in_detail);
  }
  public static function error($x, $file="*", $line="*", $in_detail=false){
    self::text('error', json_encode($x), $file, $line, $in_detail);
  }

  private static function text ($type, $x, $file, $line, $in_detail){
    $class = 'Log';
    $str = "\n[TYPE] - ".strtoupper($type)."\n".
    "[PLACE] - ".$file.":".$line."\n".
    "[VAL] - ".json_encode($x)."\n";

    if($in_detail){
      $str += self::drip($file, $line)."\n";
    }

    $class::$type($str);
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
