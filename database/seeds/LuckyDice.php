<?php


use App\Util\Util;
class LuckyDice {
  public static function japanese_fullnames(){
    return self::drip('japanese_fullnames', 'tsv');
  }
  public static function japanese_names(){
    return self::drip('japanese_names', 'tsv');
  }
  public static function blood_types(){
    return self::drip('blood_types', 'tsv');
  }
  public static function ages(){
    return self::drip('ages', 'tsv');
  }
  public static function phone_numbers(){
    return self::drip('phone_numbers', 'tsv');
  }
  public static function cellphone_numbers(){
    return self::drip('cellphone_numbers', 'tsv');
  }
  public static function prefectures(){
    return self::drip('prefectures', 'tsv');
  }
  public static function curry_ways(){
    return self::drip('curry_ways', 'tsv');
  }
  public static function emails(){
    return self::drip('emails', 'tsv');
  }
  public static function birthdays(){
    return self::drip('birthdays', 'tsv');
  }
  public static function facebooks(){
    return self::drip('facebooks', 'tsv');
  }
  public static function twitters(){
    return self::drip('twitters', 'tsv');
  }
  public static function pictures(){
    return self::drip('pictures', 'tsv');
  }
  public static function anime_titles(){
    return self::drip('anime_titles', 'tsv');
  }



  public static function name(){
    return ;
  }
  public static function nickname(){
    return ;
  }
  public static function fullname(){
    return ;
  }
  public static function japanese_nickname(){
    return ;
  }
  public static function anime_name(){
    return ;
  }
  public static function big_picture(){
    return ;
  }
  public static function small_picture(){
    return ;
  }
  public static function twitter(){
    return ;
  }





  private static function drip ($name, $ext) {
    $lines = file(__ROOT__.'database/seeds/data/'.$name.'.'.$ext);
    return $lines[rand(0, count($lines)-1)];
  }
}
