<?php namespace App\Models;

use App\Util\NuLog;
use App\Util\Util;
use App\Models\User;

class Message {

  public static function all(){
    $db = new MysqlAdaptor();
    $res = $db->select("messages", []);
    return $res['data'];
  }

  public static function all_with_user() {
    $messages = [];
    foreach(self::all() as $message){
      $user = User::search(["id" => $message["sender_id"]])[0];
      unset($user['id']);
      array_push($messages, array_merge($message, $user));
    }
    return $messages;
  }

  public static function group_history($issue_id){
    $db = new MysqlAdaptor();
    $sql1 = "SHOW TABLES FROM " . DB_DATABASE . " LIKE 'messages';";
    $res1 = $db->sql($sql1);

    if($res1['flag']){
      $sql2 = "SELECT m.id AS id, m.body, u.full_name, u.picture, m.created_at, u.id AS sender_id FROM messages AS m ".
        "LEFT JOIN users AS u ON m.sender_id = u.id ".
        "WHERE m.issue_id = ".$issue_id.";";
      $res2 = $db->sql($sql2);

      if($res2['flag']){
        return $res2['data'];
      } else {
        return [];
      }
    } else {
      NuLog::error($sql1." was failed.", __FILE__, __LINE__);
      return [];
    }
  }

  // messages
  //  receiver_id, sender_id, body, read_flag
  public static function latests($me){
    $db = new MysqlAdaptor();
    $sql1 = "SHOW TABLES FROM " . DB_DATABASE . " LIKE 'messages';";
    $res1 = $db->sql($sql1);

    if($res1['flag']){

      $filtered_sorted_messages_sql =
        "(SELECT a.id, receiver_id, sender_id, body, read_flag, a.created_at FROM (".
          "SELECT * FROM messages ORDER BY messages.created_at DESC".
        ") AS a WHERE sender_id = " . $me . " GROUP BY receiver_id)".

        " UNION ".

        "(SELECT a.id, sender_id, receiver_id, body, read_flag, a.created_at FROM (".
          "SELECT * FROM messages ORDER BY messages.created_at DESC".
        ") AS a WHERE receiver_id = " . $me . " GROUP BY sender_id)";
        // sender_id行にmeを寄せて、receiver_idに相手が寄っている。重複はある。

      $unique_messages =
        "SELECT m.id AS id, receiver_id, sender_id, body, picture, full_name, m.created_at AS created_at FROM (".
          "SELECT * FROM (".
            "SELECT * FROM (".
              $filtered_sorted_messages_sql.
            ") AS tmp ORDER BY created_at DESC".
          ") AS tmp2 WHERE receiver_id != " . $me . " GROUP BY receiver_id".
        ") AS m LEFT JOIN users AS u ON m.receiver_id = u.id ORDER BY created_at DESC;";
      // 整列させて重複を削除。最新メッセージのみが残る

      $sql2 = $unique_messages;

      $res2 = $db->sql($sql2);
      if($res2['flag']){
        return $res2['data'];
      } else {
        NuLog::error($unique_messages." was failed.", __FILE__, __LINE__);
        return [];
      }
    } else {
      NuLog::error($sql1." was failed.", __FILE__, __LINE__);
      return [];
    }
  }

  public static function separated_history($you, $me){
    $db = new MysqlAdaptor();

    $sql =
      "SELECT m.*, u.id AS user_id, u.picture, u.full_name FROM messages AS m ".
        "INNER JOIN users AS u ON m.sender_id = u.id ".
        "WHERE (m.type = 'separated' AND sender_id = ".$you." AND receiver_id = ".$me.") ".
        "OR (m.type = 'separated' AND sender_id = ".$me." AND receiver_id = ".$you.") ".
        "ORDER BY id ASC";

    $res = $db->sql($sql);
    if($res['flag']){
      return $res['data'];
    } else {
      return [];
    }
  }

}
