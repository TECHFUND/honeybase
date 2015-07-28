<?php


/*
* ENV VAL
*/
$env = getenv('GROWTHER_ENV');
if($env === false){
  $env = "s";
}

/*
* ENV CONFIG
*/
$_data = App\Util\Util::getJSON(__DIR__.'/config.json');
$data = null;
if($env == "p"){
  $data = $_data->production;
} else if($env == "s") {
  $data = $_data->staging;
} else {
  $data = $_data->development;
}

$_mail = App\Util\Util::getJSON(__DIR__.'/mail.json');


/*
* DEF CONSTANT
*/
if( !defined('__ROOT__') ) { //2度定義しないように
  /* path */
  define("__ROOT__", __DIR__.'/../');
  define("__CONFIG__", __DIR__.'/');
  define("__PUBLIC__", __ROOT__.'public/');
  define("__RESOURCES__", __ROOT__.'resources/');
  define("__IMAGE__", __PUBLIC__.'assets/img/generated/');
  define("__TECHFUNDICON__", "assets/img/common/company02.gif");
  define("__LOGINICON__", "assets/svg/approve.svg");
  define("__LOGOUTICON__", "assets/svg/user_h.svg");
  define("__GUESTICON__", "/assets/img/common/guest.png");

  /********************************
  * honeybase
  ********************************/
  /* general */
  define("SERVICE_NAME", $data->service->name);
  define("ORIGIN", $data->origin);
  define("DOMAIN", implode("", explode("http://",ORIGIN)) );

  /* database */
  define("DB_HOST", $data->database->host);
  define("DB_PORT",$data->database->host);
  define("DB_USERNAME", $data->database->username);
  define("DB_PASSWORD", $data->database->password);
  define("DB_DATABASE", $data->database->name);
  define("LOG_PATH", __DIR__.$data->database->log_path);

  /* email auth */
  define("SALT", "salt");

  /* oauth */
  define("FACEBOOK_CONSUMER_KEY", $data->oauth->facebook->consumer_key);
  define("FACEBOOK_CONSUMER_SECRET", $data->oauth->facebook->consumer_secret);
  define("__ENV__", $env);

  /* basic auth */
  define("BASIC_PASS", "root");

  /* .env */
  define("APP_ENV", "local");
  define("APP_DEBUG", true);
  define("APP_KEY", "mypage");
  define("APP_LOCALE", "en");
  define("APP_FALLBACK_LOCALE", "env");

  /* util */
  define("ENTERCOMMENT", "shift+enter:new-line&#13;&#10;enter:submit");


  /* mail */

  define('ROOT_URL', ORIGIN);									// サイトURL
  define('MAIL_FROM', "Growther <info@".DOMAIN.">");				// システムから送られるメールの送信元
  define('MAIL_FROM_ADDRESS', "<info@".DOMAIN.">");							// システムから送られるメールの送信元(アドレスのみ)
  define('ADMIN_ADDRESS', "info@techfund.jp");								// 実際に送られる管理者のアドレス
  define('RETURN_PATH', "-f info@".DOMAIN);									// Return-path
  define('ORG', mb_internal_encoding());	// 元のエンコーディングを保存
  mb_language("japanese");		// エンコーディング
  mb_internal_encoding("UTF-8");// エンコーディング

  define('MAIL_HEADERS', 			// メールヘッダ
  	"MIME-Version: 1.0\r\n"
  	  . "Message-Id: <" . md5(uniqid(microtime())) . "@".DOMAIN.">\r\n"
  	  . 'From: "Growther"' . MAIL_FROM_ADDRESS . "\r\n"
  	  .	'Reply-To: ' . MAIL_FROM_ADDRESS . "\r\n"
  	  .	'X-Mailer: PHP/' . phpversion()
  );

  define('MAIL_COMMON_FOOTER', 			// メール共通フッタ
  		"\n" .
  		"専門家とクライアントのマッチング\n" .
  		"コンサルタントマッチングサービス - Growther\n" .
  		"\n" .
  		"*************************\n" .
  		"Growther\n" .
  		ROOT_URL . "\n" .
  		"■お問い合わせ先 \n" .
  		MAIL_FROM . "\n" .
  		"*************************"
  );

  define('RECOMMENDATION_BODY', implode("\n", $_mail->call_for_expert->body).MAIL_COMMON_FOOTER);
  define("VERIFY_BODY", implode("\n", $_mail->verify_for_signup->body).MAIL_COMMON_FOOTER);

}
