<?php namespace HoneyBase\Core\Model;

use Util\Util\NuLog;
use Util\Util\Util;
use Util\Util\CommonFunctions;

class Uploader {

  public static function uploadFile($key){
    $path = Util::createRandomString(100);
    $isUploaded = move_uploaded_file($_FILES[$key]['tmp_name'], __IMAGE__.$path);
    if($isUploaded){
      $ext = self::getFilenameExtension(__IMAGE__.$path);
      self::checkScriptOrNot($path);
      $_path = $path.".".$ext;
      $isRenamed = rename(__IMAGE__.$path, __IMAGE__.$_path);
      if($isRenamed){
        return $_path;
      } else {
        NuLog::error("image rename error",__FILE__,__LINE__);
        return null;
      }
    } else {
      NuLog::error("image write error",__FILE__,__LINE__);
      return null;
    }
  }

  private static function getFilenameExtension($tmp_name){
    $info = getimagesize($tmp_name);
    switch ($info['mime']) {
    case 'image/gif':
        $mime = $ext = 'gif';
        break;
    case 'image/png':
        $mime = $ext = 'png';
        break;
    case 'image/jpeg':
        $mime = 'jpeg';
        $ext  = 'jpg';
        break;
    default:
        throw new RuntimeException('この種類の画像形式は受理できません。');
    }
    return $ext;
  }

  private static function checkScriptOrNot($filename){
    $lines = file(__IMAGE__.$filename);
    $firstline = $lines[0];
    mb_regex_encoding('ASCII');
    if (mb_eregi('<\\?php', $firstline)) {
      die('Attack detected');
      NuLog::error('Attack detected');
    }
    mb_regex_encoding('ASCII');
    if (mb_eregi('^.*<\\?php.*$', $firstline)) {
      die('Attack detected');
      NuLog::error('Attack detected');
    }
    if (preg_match('/<\\?php./i', $firstline)) {
      die('Attack detected');
      NuLog::error('Attack detected');
    }
  }

}
