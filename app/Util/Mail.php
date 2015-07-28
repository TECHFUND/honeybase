<?php namespace App\Util;

use App\Models\Issue;
use App\Models\User;

class Mail {

  public static function recommendation_mail($issue_id, $expert_id){
    $issue = Issue::find($issue_id);
    $client = User::find($issue['user_id']);
    $expert = User::find($expert_id);

    $cf = new CommonFunctions();

    $_mail = Util::getJSON(__CONFIG__.'/mail.json');
    $replace_str_arr = array(
      "expert_name" => $expert['full_name'],
      "client_name" => $client['full_name'],
      "issue_name" => $issue['title'],
      "issue_body" => substr($issue['body'], 0, 50)."...",
      "chat_link" => ROOT_URL."/expert/issues/".$issue_id."/chat"
    );

    $mail_body = $cf->mailStrReplace($replace_str_arr, RECOMMENDATION_BODY);   // 変換用メソッド呼び出し

    $res = mb_send_mail($expert['email'], $_mail->call_for_expert->title, $mail_body, MAIL_HEADERS);
    mb_internal_encoding(ORG);// エンコーディングを戻す

    return $res;
  }

  public static function verify_mail($email, $verify_code){
    $cf = new CommonFunctions();
    $_mail = Util::getJSON(__CONFIG__.'/mail.json');
    $replace_str_arr = array(
      'email'=>$email,
      'link'=>ROOT_URL.'/api/v1/email_verify?'."code=".$verify_code,
    );


    $mail_body = $cf->mailStrReplace($replace_str_arr, VERIFY_BODY);

    $res = mb_send_mail($email, $_mail->verify_for_signup->title, $mail_body, MAIL_HEADERS); // このURLを押してアクティブ！
    NuLog::info($email);
    NuLog::info($_mail->verify_for_signup->title);
    if($res){
      NuLog::info('mail success');
    } else {
      NuLog::info('mail failed');
    }
    mb_internal_encoding(ORG);// エンコーディングを戻す
    return $res;
  }

  public static function send($mail_name, $params){
    $_mail = Util::getJSON(__CONFIG__.'/mail.json')->$mail_name;
    $cf = new CommonFunctions();
    $body = implode("\n", $_mail->body).MAIL_COMMON_FOOTER;
    $mail_body = $cf->mailStrReplace($params, $body);

    $res = mb_send_mail($params['email'], $_mail->title, $mail_body, MAIL_HEADERS); // このURLを押してアクティブ！
    mb_internal_encoding(ORG);// エンコーディングを戻す
    return $res;
  }
}
