<?php

use Illuminate\Database\Seeder;
use App\Models\MysqlAdaptor;
use App\Models\User;
use App\Models\Team;
use App\Models\Feed;
use App\Models\Message;
use App\Util\NuLog;
use App\Util\Util;
include 'LuckyDice.php';
//use Illuminate\Database\Eloquent\Model;

define("MAX", 10);

class DevelopmentSeeder extends Seeder {

	public function run() {
    $db = new MysqlAdaptor();
    for($i=0; $i < MAX; $i++){
      $techfund_id = (string)($i+1); // int入れるとschemeが固まっちゃってエラーの元になる
      $user = [
        "social_id"=>Util::createRandomString(10),
        "type"=>"",
        "created_at"=>Util::ms(),
        "updated_at"=>Util::ms(),
        "email"=>LuckyDice::emails(),
        "team_id"=>rand(1, MAX),
        "picture"=>LuckyDice::pictures(),
        "user_access_token"=>Util::createRandomString(25),
        "full_name"=>LuckyDice::japanese_fullnames(),
        "facebook_link"=>LuckyDice::facebooks(),
				"verify"=>"true",
				"techfund_id"=>"",
				"description"=>"",
				"first_name"=>"",
				"last_name"=>""
      ];
      $team = [
        "name"=>LuckyDice::anime_titles(),
        "founder_techfund_id"=>$techfund_id,
        "team_cover"=>LuckyDice::pictures(),
				"owner_id"=>rand(1,5),
        "link"=>LuckyDice::twitters(),
        "description"=>LuckyDice::curry_ways(),
        "created_at"=>Util::ms(),
        "updated_at"=>Util::ms()
      ];
      $feed = [
        "text"=>LuckyDice::anime_titles()."は、".LuckyDice::curry_ways()."。",
        "user_id"=>[rand(1,MAX), MAX+1][rand(0, 1)],
        "notification_flag"=>[true, false][rand(0, 1)],
        "created_at"=>Util::ms()-3000000-($i*1000000),
        "updated_at"=>Util::ms()-3000000-($i*1000000)
      ];
    	for($j=0; $j < MAX*10; $j++){
	      $message = [
	        "body"=>LuckyDice::anime_titles()."でしばしば用いられるカレーの食べ方は、".LuckyDice::curry_ways()."で".($i*$j)."回かきまぜ。",
	        "sender_id"=>[rand(1,MAX), MAX+1][rand(0, 1)], //テスターかロボットからランダム
	        "receiver_id"=>[rand(1,MAX), MAX+1][rand(0, 1)],
	        "read_flag"=>[true, false][rand(0,1)],
	        "created_at"=>Util::ms()-3000000-($i*$j*1000),
	        "updated_at"=>Util::ms()-3000000-($i*$j*1100)
	      ];
	      $db->insert("messages", $message);
			}

      $event = [
        "title"=>"test event ".$i,
        "description"=>DUMMY_TEXT,
        "picture"=>LuckyDice::pictures(),
        "date"=>Util::ms()+3000000,
        "created_at"=>Util::ms()-3000000-($i*1000000),
        "updated_at"=>Util::ms()-3000000-($i*1000000)
      ];
      $db->insert("events", $event);

    	for($j=0; $j < MAX*10; $j++){
	      $user_event = [
					"user_id"=>[1, rand(1,MAX), MAX+1][rand(0, 2)],
					"event_id"=>[1, rand(1,MAX), MAX+1][rand(0, 2)],
	        "created_at"=>Util::ms()-3000000-($i*1000000),
	        "updated_at"=>Util::ms()-3000000-($i*1000000)
	      ];
	      $db->insert("user_events", $user_event);
			}

      $question = [
        "body"=>"Do you like'".$i."'?",
        "event_id"=>[1, rand(1,MAX), MAX+1][rand(0, 2)],
        "priority"=>rand(0, 2),
        "created_at"=>Util::ms()-3000000-($i*1000000),
        "updated_at"=>Util::ms()-3000000-($i*1000000)
      ];
      $db->insert("questions", $question);

    	for($j=0; $j < MAX; $j++){
	      $answer = [
	        "label"=>LuckyDice::anime_titles(),
	        "question_id"=>[1, rand(1,MAX), MAX+1][rand(0, 2)],
	        "priority"=>rand(0, 2),
	        "created_at"=>Util::ms()-3000000-($i*1000000),
	        "updated_at"=>Util::ms()-3000000-($i*1000000)
				];
	      $db->insert("answers", $answer);

	      $user_answer = [
	        "user_id"=>[rand(1,MAX), MAX+1][rand(0, 1)],
	        "answer_id"=>rand(1,MAX*MAX),
	        "created_at"=>Util::ms()-3000000-($i*1000000),
	        "updated_at"=>Util::ms()-3000000-($i*1000000)
	      ];
      	$db->insert("user_answers", $user_answer);
			}


      $db->insert("users", $user);
      $db->insert("teams", $team);
      $db->insert("feeds", $feed);
    }
	}
}
