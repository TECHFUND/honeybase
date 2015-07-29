<?php namespace Util\Util;

use Util\Util\NuLog;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;

class FB {
  public static function api($path, $token){
    FacebookSession::setDefaultApplication(FACEBOOK_CONSUMER_KEY, FACEBOOK_CONSUMER_SECRET);
    $session = new FacebookSession($token);

    if($session) {
      try {
        $me_request = new FacebookRequest($session, 'GET', $path);
        $obj = $me_request->execute()->getGraphObject();
        return $obj->asArray();
      } catch(FacebookRequestException $e) {
        $code = $e->getCode();
        $msg = $e->getMessage();
        NuLog::error($msg, __FILE__, __LINE__);
        return null;
      }
    }
  }
}
