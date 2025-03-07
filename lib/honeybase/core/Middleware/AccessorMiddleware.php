<?php namespace HoneyBase\Core\Middleware;

use Closure;
use HoneyBase\Core\Model\User;
use Util\Util\NuLog;
use Util\Util\Util;

class AccessorMiddleware {

  public function handle($request, Closure $next){

    // setAccessorはobjectを食わせる関数になったので、Middleware上でgetJSONする必要がある
    // getJSONが相対パスなのかROOTからのパスなのかはコメントかドキュメントで示してやらないと困惑する
    // requestをコンストラクタで食わせる設計はなくなったのでこのコードはおそらく全面的に描き直される
    // requestからの抽出物を食わせる形になる
    // いっぺんintegration testを実行してから、Middlewareを実装しなおして緑にする流れ
    $parser = new AccessorParser($request);
    if( $request->input("isTest") ){
      $type = $request->input("testType");
      $parser->setAccessor('lib/honeybase/test/integration/accessors/'.$type.'.json');
    } else {
      $parser->setAccessor('app/accessor.json');
    }
    $header = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];


    $parser->setTableName();
    $parser->setPostedValue(); // value check
    $parser->setCurrentUser(); // current_user
    $current_user = $parser->getCurrentUser();

    $accessor = $parser->getAccessor(); // accessorをpath->table->role&paramsと走査する

    $isAllPermit = $parser->isAllPermit();
    if($isAllPermit === null) {
      // "*" 定義がないのでスルー
    } elseif ($isAllPermit) {
      return $next($request);
    } else {
      $msg = "All access denying now.";
      NuLog::error(["context"=>$msg, "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
      return response(['flag'=>false, "error_message"=>$msg], 503, $header);
    }

    $target_path = $parser->reffererWildcardize(); // 実行pathが許可されているかチェック

    if( property_exists($accessor, $target_path) ){
      $parser->checkAuthOption();

      $isPermit = $parser->checkHoneyBaseFunction(); // honeybase直下関数の許諾
      if ($isPermit) {
        $request = $parser->getRequestValuable();
        return $next($request);
      }

      $parser->setDatabase();
      $isValidTable = $parser->checkOwnerAlias(); // owner_idを独自定義しているtableを解析して後で使えるようにする
      $_database = $parser->getDatabase();
      $table_name = $parser->getTableName();
      if( $isValidTable && property_exists($_database, $table_name) ){
        $path_action = $parser->action;
        $_table = $_database->$table_name;
        $parser->setTable($_table);
        if( property_exists($_table, $path_action) ){
          $_action = $_table->$path_action;
          $parser->setAction($_action);
          if( property_exists($_action,"role") && property_exists($_action,"params") ){
            $parser->checkDefaultParamsValue();
            $parser->denyInvalidParams(); // http request paramsが不正なkeyにアクセスしていたら遮断
            $isPermit = $parser->roleFilter(); // role別フィルタ
            if($isPermit){
              $request = $parser->getRequestValuable();
              return $next($request);
            } else {
              $msg = "Bad role.";
              NuLog::error(["context"=>$msg, "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
              return response(['flag'=>false, "error_message"=>$msg], 503, $header);
            }
          } else {
            $msg = "No role or params in action definition.";
            NuLog::error(["context"=>$msg, "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
            return response(['flag'=>false, "error_message"=>$msg], 503, $header);
          }
        } else {
          $msg = "No action in table definition.";
          NuLog::error(["context"=>$msg, "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
          return response(['flag'=>false, "error_message"=>$msg], 503, $header);
        }
      } else {
        $msg = "No table in path definition.";
        NuLog::error(["context"=>$msg, "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
        return response(['flag'=>false, "error_message"=>$msg], 503, $header);
      }
    } else {
      $msg = "No path in accessor definition.";
      NuLog::error(["context"=>$msg, "user_id"=>(isset($current_user)) ? $current_user['id'] : -1], __FILE__, __LINE__);
      return response(['flag'=>false, "error_message"=>$msg], 503, $header);
    }
  }
}
