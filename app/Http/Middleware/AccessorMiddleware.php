<?php namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Util\NuLog;
use App\Util\Util;

class AccessorMiddleware {

  public function handle($request, Closure $next){

    $parser = new AccessorParser($request);
    $header = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];


    $parser->setTableName();
    $parser->setPostedValue(); // value check
    $parser->setCurrentUser(); // current_user

    $accessor = $parser->getAccessor(); // accessorをpath->table->role&paramsと走査する

    $isAllPermit = $parser->isAllPermit();
    if($isAllPermit === null) {
      // "*" 定義がないのでスルー
    } elseif ($isAllPermit) {
      return $next($request);
    } else {
      $msg = "All access denying now.";
      NuLog::error($msg, __FILE__, __LINE__);
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
      if( $isValidTable ){
        $path_action = $parser->action;
        $table_name = $parser->getTableName();
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
              NuLog::error($msg, __FILE__, __LINE__);
              return response(['flag'=>false, "error_message"=>$msg], 503, $header);
            }
          } else {
            $msg = "No role or params in action definition.";
            NuLog::error($msg, __FILE__, __LINE__);
            return response(['flag'=>false, "error_message"=>$msg], 503, $header);
          }
        } else {
          $msg = "No action in table definition.";
          NuLog::error($msg, __FILE__, __LINE__);
          return response(['flag'=>false, "error_message"=>$msg], 503, $header);
        }
      } else {
        $msg = "No table in path definition.";
        NuLog::error($msg, __FILE__, __LINE__);
        return response(['flag'=>false, "error_message"=>$msg], 503, $header);
      }
    } else {
      $msg = "No path in accessor definition.";
      NuLog::error($msg, __FILE__, __LINE__);
      return response(['flag'=>false, "error_message"=>$msg], 503, $header);
    }
  }
}
