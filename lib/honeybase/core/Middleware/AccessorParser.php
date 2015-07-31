<?php namespace HoneyBase\Core\Middleware;

use Util\Util\NuLog;
use Util\Util\Util;
use HoneyBase\Core\Model\User;
use Exception;

class AccessorParser {

  public $header;
  public $request;
  public $data;
  public $value;
  public $user_id;
  public $current_user;
  public $path;
  public $action;
  public $refferer;
  public $accessor;
  public $_honeybase;
  public $target_path;
  public $table_name;
  public $_database;
  public $_path;
  public $_role;
  public $_params;
  public $_action;
  public $defined_honeybase_actions;
  // _ から始まる変数はconfig.json 由来

  function __construct ($request) {
    $this->request = $request;
    $this->data = $request->all();
    $this->path = $request->path();
    $path_array = explode("/", $this->path);
    $this->action = array_pop($path_array);
    $this->refferer = $this->data['refferer'];
    $this->defined_database_actions = ["insert", "update", "select", "delete", "search"];
    $this->defined_honeybase_actions = ["signup", "signin", "auth", "logout", "current_user", "uploader"];
  }

  public function setTableName () {
    if( array_key_exists("table", $this->data) ){
      $this->table_name = $this->data['table'];
    } else {
      return "";
    }
  }
  public function getTableName () {
    return $this->table_name;
  }

	public function setPostedValue() {
    try {
      if(array_key_exists("value",$this->data)){
        $value = json_decode($this->data['value']);

        if( in_array($this->action, $this->defined_database_actions) ){
          if($value == null){
            throw new Exception("Value parse error");
          } else {
            $this->value = $value;
          }
        } else {
          $this->value = null;
        }
      } else {
        $this->value = null;
      }
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
  }

  public function getRequest(){
    return $this->request;
  }

  public function setCurrentUser(){
    $session_id = $this->request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    if($current_user != null){
      // my以下の全てのリクエストでcurrent_userを取得しやすいようにするヘルパー
      $this->request->current_user = $current_user;
    }
    $this->current_user = $current_user;
    return $current_user;
  }

  public function setAccessor($rel_path){
    $this->accessor = Util::getJSON(__ROOT__. $rel_path);
  }

  public function getAccessor(){
    try {
      if($this->accessor == null){
        throw new Exception("Accessor parse error");
      } else {
        return $this->accessor;
      }
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
  }

  public function isAllPermit() {
    $res = null;
    if( isset($this->accessor) && property_exists($this->accessor, "*") ) {
      $path = "*";
      $res = $this->accessor->$path;
    }
    return $res;
  }

  public function reffererWildcardize(){
    // 今のreffererにidが含まれている場合、それを*で置換する関数
    $this->target_path = $this->refferer;

    foreach($this->accessor as $__key => $__value){

      // *を含む定義とreffererを比較して、一致したら返す。
      if( strpos($__key, "*") && $this->refferer != "/" ){
        $ereg_path_define = "/^".str_replace("/", "\/", $__key)."$/";
        $r = str_replace("*", "\d*", $ereg_path_define);
        if ( preg_match($r, $this->refferer) ) {
          $this->target_path = $__key;
          return $this->target_path;
        } else {
          // *は含むがマッチしなかったaccessor pathたち
        }
      } else if ($__key == $this->refferer) {
        return $this->refferer;
      } else {
      }
    }
    // どことも一致せずに処理がここまできたら、pathが定義されていないエラー処理
  }

  public function checkAuthOption(){
    try {
      $target_path = $this->target_path;
      $this->_path = $this->accessor->$target_path; // あるpathに対する定義
      $this->_honeybase = $this->_path->honeybase; // honeybase系関数の設定
      $_path = $this->_path;
      $_honeybase = $this->_honeybase;


      // $_honeybaseのauth初期値をparseして再代入する処理
      $filtered = [];
      foreach($_honeybase as $honeybase_action){
        $isOption = strpos($honeybase_action, "[") !== false;
        $isAuth = strpos($honeybase_action, "auth") !== false || strpos($honeybase_action, "signup") !== false;
        if ($isAuth && $isOption) {
          array_push($_honeybase, "auth");
          array_push($_honeybase, "signup");

          // honeybase.authの第一引数であるOAuthプロバイダ名
          if( array_key_exists("provider", $this->data) ){
            $filtered['provider'] = $this->data['provider'];
          }

          // honeybase.authの第二引数のオプションを取り出す
          $options = (object)array();
          if( array_key_exists('option', $this->data) ){
            $options = json_decode($this->data['option']);
          }
          if( array_key_exists("user_access_token", $this->data) ){
            $options->user_access_token = $this->data['user_access_token'];
          }

          // accessor.jsonで定義したauthの初期値でオプションを上書きしてコントローラーに渡す
          $default_value_array = explode(",", explode("]", explode("[", $honeybase_action)[1])[0]);
          foreach($default_value_array as $str){
            $arr = explode("=", $str); $_key = $arr[0]; $_value = $arr[1];
            if($_key == "provider"){
              $filtered['provider'] = $_value;
            } elseif ($_key == "type"){ // auth[type=(writer|editor)]で設定しているときに、ユーザー側からのadminログインを許可しない仕組み
              if( strpos($_value, "(") !== false && strpos($_value, ")") !== false ){
                if( strpos($_value, "(") == 0 && strpos($_value, ")") == strlen($_value)-1 ){
                  $inside = explode("(", explode(")", $_value)[0])[1];
                  $types = explode("|", $inside);

                  if ( property_exists($options, $_key) ) {
                    if( in_array($options->$_key, $types) ) {
                      // auth/signupがフィルターをクリア
                    } else {
                      throw new Exception("invalid user type");
                    }
                  } else {
                    // signup等でtypeをフロントが渡してない
                  }
                } else {
                  throw new Exception("invalid definition");
                }
              } else {
                // 括弧がないノーマル文字列なので、初期値として保存する
                $options->$_key = $_value;
              }
            }
          }
          $filtered['option'] = $options;
          $this->request->filtered = $filtered; // authの場合、all()ではなく初期値処理したfilteredの変数をコントローラーで使用する
        }
      }
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
  }

  public function checkHoneyBaseFunction(){
    try {
      if( $this->isHoneyBaseAction($this->defined_honeybase_actions, true) ) {
        if( $this->isHoneyBaseAction($this->_honeybase, false) ){
          return true;
        } else {
          throw new Exception("you did honeybase action but not allowed");
        }
      } else {
        return false;
      }
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
  }

  private function isHoneyBaseAction ($honeybase_actions, $isTest) {
    // databaseアクションではなくhoneybaseアクションであることを確認
    $result = false;
    $isMatchedAction = false;
    foreach($honeybase_actions as $item) {
      if($isTest){
        $isMatchedAction = (strpos($this->action, $item) !== false);
      } else {
        $isMatchedAction = (strpos($item, $this->action) !== false);
      }

      if($result || $isMatchedAction){
        $result = true;
      }
    }
    return $result;
  }

  public function setDatabase() {
    try {
      $_path = $this->_path;
      if( property_exists($_path, "database") ){
        $this->_database = $_path->database;
      } else {
        throw new Exception("honeybase definition only, but not matched");
      }
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
  }

  public function getDatabase(){
    return $this->_database;
  }

  public function checkOwnerAlias(){
    $res = false;
    $tbl = $this->table_name;
    foreach($this->getDatabase() as $_key => $_value){
      $hasOption = strpos($_key, "[") !== false;
      if($hasOption){
        // table定義がオプションを持っている時
        if( explode("[", $_key)[0] === $tbl ){
          // "["より前が完全一致
          $inside_str = explode("]" ,explode("[", $_key)[1])[0];
          $owner_alias = explode("=", $inside_str)[1];
          $this->_database->$_key->owner_alias = $owner_alias; // owner_aliasをdatabaseオブジェクトに保持
          $new_table_name = $tbl."[user_id=".$owner_alias."]";
          $this->table_name = $new_table_name;
          $res = true; // 一度$resがtrueになったら何があってもfalse上書きされなくしたい
        } else {
        }
      } else {
        $res = true;
      }
    }
    return $res;
  }

  public function setTable($_table){
    $this->_table = $_table;
  }



  public function checkDefaultParamsValue() {
    try {
      $this->_role = $this->_action->role;
      $this->_params = $this->_action->params;
      // 両方nullやん！！！！！！

      $_role = $this->_role;
      $_params = $this->_params;
      $current_user = $this->current_user;

      /*
      * default_valueをチェックして保存する値を上書き
      */
      foreach($_params as $_value){
        if(strpos($_value, "=") !== false){
          $array = explode("=", $_value); // parse
          $default_key = $array[0];
          $default_value = $array[1];

          if($default_value === "true" || $default_value === "false") {
            $default_value = ($default_value === "true"); //文字列をbooleanに
          } else if ($default_value === "NULL" || $default_value === "null" || $default_value === "Null" || $default_value === "nil" || $default_value === "Nil") {
            $default_value = null;
          } else if (gettype($default_value) == "int") {
            $default_value = (int)$default_value;
          }

          /*
          * もし$default_valueが{}を含んでいたら、文字列として上書き保存する前に先にそっちをparseして、
          * 中身がcurrent_userだったら$current_user['id']を保存する
          */
          if( strpos($default_value, "{") !== false && strpos($default_value, "}") !== false ){
            if( strpos($default_value, "{") == 0 && strpos($default_value, "}") == count($default_value) ){
              $content = substr($default_value, 1, count($default_value)-1); // 文頭文末に{}が存在するとき
              if($content == "current_user"){
                if( isset($current_user) ){
                  $value->$default_key = $current_user['id']; // 初期値を上書き
                } else {
                  throw new Exception("this request needs current_user default value but not logged in"); // no current user
                }
              } else {
                throw new Exception("invalid default value (wierd inside value)"); // {}の内側の値がおかしいとき
              }
            }
          } else {
            $this->value->$default_key = $default_value; // {}が文頭文末にない場合はスルーして文字列初期値として保存する（プロトタイピング）
          }
          array_push($_params, $default_key); // paramsに左辺を追加してバリデーター回避
        }
      }
      $this->request->value = $this->value;// 上書き値を適用
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
  }


  public function denyInvalidParams(){
    try {
      if( isset($this->value) ){
        foreach($this->value as $_key => $_value){
          if( !$this->isValidParam($_key) ){ // =値をうまくとれてない
            throw new Exception("bad parameters");
          }
        }
      }
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
  }

  private function isValidParam($_key) {
    $result = false;
    $isMatchedAction = false;
    foreach($this->_params as $param) {
      $isMatchedAction = (strpos($param, $_key) !== false);
      if($result || $isMatchedAction){
        $result = true;
      }
    }
    return $result;
  }


  public function loginFilter(){
    try {
      if($this->current_user != null){
        return true;
      } else {
        throw new Exception("required login but not logged in");
      }
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
  }


  public function ownerFilter(){
    try {
      // 下記のようなowner定義aliasを実装した
      /*
        "hoges[user_id=owner_id]" : {}
      */
      $owner_id_key = (isset($this->_table->owner_alias)) ? $this->_table->owner_alias : "user_id";
      if($owner_id_key == "id"){
        $owner_id = $this->data['id'];
      } else if ($owner_id_key == "user_id") {
        $owner_id = $this->value->$owner_id_key;
      } else {
        $owner_id = null;
      }
      if($this->current_user['id'] == $owner_id){
        return true;
      } else {
        if($this->current_user == null){
          setcookie(SERVICE_NAME.'id', '', time() - 3600, '/');
          throw new Exception("required owner role but not logged in");
        } else {
          throw new Exception("required owner role but you're not owner");
        }
      }
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
  }

  public function roleFilter(){
    try {
      if ($this->_role == "all") {
        return true;
      } elseif ($this->_role == "owner") {
        return $this->ownerFilter();
      } elseif ($this->_role == "login") {
        return $this->loginFilter();
      } elseif($this->_role == $this->current_user['type']){
        return true;
      } else {
        throw new Exception("none role or something wrong");
      }
    } catch (Exception $e) {
      NuLog::error($e->getMessage(), __FILE__, __LINE__);
    }
  }

  public function getRequestValuable(){
    return $this->request;
  }

  public function setAction($_action){
    $this->_action = $_action;
  }
}
