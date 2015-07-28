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

define("MAX", 15);

class MyPageSeeder extends Seeder {

	public function run() {
    $db = new MysqlAdaptor();
    for($i=0; $i < MAX; $i++){
      $techfund_id = (string)($i+1); // int入れるとschemeが固まっちゃってエラーの元になる
			$user_type = ["client", "expert"][rand(0, 1)];
      $user = [
        "full_name"=>LuckyDice::japanese_fullnames(),
				"description"=>LuckyDice::anime_titles()."\n".LuckyDice::anime_titles()."\n".LuckyDice::anime_titles()."\n".LuckyDice::anime_titles()."\n".LuckyDice::anime_titles(),
        "email"=>LuckyDice::emails(),
        "picture"=>LuckyDice::pictures(),
        "type"=>$user_type,
				"banned"=>[true, false][rand(0, 1)],
        "social_id"=>Util::createRandomString(10),
        "facebook_link"=>LuckyDice::facebooks(),
        "user_access_token"=>Util::createRandomString(25),
        "created_at"=>Util::ms(),
        "updated_at"=>Util::ms(),
	      'encrypted_password'=>'xxxxxxxxxxxxxxxxxxxxxxxx',
	      'salt'=>SALT,
	      'email_verify'=>false,
	      'email_verify_code'=>'xxxxxxxxxxxxxxxxxxxxxxxxxxxx'
      ];
      $user_id = $db->insert("users", $user)['id'];
			if($user_type == "expert"){
				$expert_id = $user_id;

				//
				$expert = [
					"user_id"=>$expert_id,
					"company"=>LuckyDice::anime_titles(),
					"position"=>LuckyDice::programing_languages(),
					"verify"=>[true, false][rand(0, 1)],
	        "created_at"=>Util::ms(),
	        "updated_at"=>Util::ms()
				];
      	$db->insert("experts", $expert);

				//
				$skill = [
					"name"=>LuckyDice::programing_languages(),
	        "created_at"=>Util::ms(),
	        "updated_at"=>Util::ms()
				];
      	$skill_id = $db->insert("skills", $skill)['id'];

				//
				$expert_skill = [
					"expert_id"=>$expert_id,
					"skill_id"=>$skill_id,
					"grade"=>rand(1,5),
	        "created_at"=>Util::ms(),
	        "updated_at"=>Util::ms()
				];
      	$db->insert("expert_skills", $expert_skill);

			} else if ($user_type == "client") {
				$client_id = $user_id;

				//
	      $issue = [
	        "title"=>LuckyDice::anime_titles(),
					"body"=>LuckyDice::programing_languages()."言語がわかりません",
					"category1"=>rand(1, 5),
					"category2"=>rand(1, 5),
	        "user_id"=>$client_id,
	        "created_at"=>Util::ms(),
	        "updated_at"=>Util::ms()
	      ];
      	$issue_id = $db->insert("issues", $issue)['id'];

				//
				for($k=0;$k<5;$k++){
					$issue_experts = [
						"issue_id"=>rand(1, 6),
						"expert_id"=>rand(1, 10),
		        "created_at"=>Util::ms(),
		        "updated_at"=>Util::ms()
					];
	      	$db->insert("issue_experts", $issue_experts)['id'];

					//
					$issue_executors = [
						"issue_id"=>rand(1, 6),
						"executor_id"=>rand(1, 10),
		        "created_at"=>Util::ms(),
		        "updated_at"=>Util::ms()
					];
	      	$db->insert("issue_executors", $issue_executors)['id'];
				}

			}



    	for($j=0; $j < MAX*4; $j++){
	      $message = [
	        "body"=>[LuckyDice::anime_titles()."でしばしば用いられるカレーの食べ方は、".LuckyDice::curry_ways()."で".($i*$j)."回かきまぜ。", LuckyDice::programing_languages()."が大好きです"][rand(0, 1)],
	        "sender_id"=>[rand(1,MAX), 1, MAX+1][rand(0, 2)], //テスターかロボットからランダム
	        "receiver_id"=>[rand(1,MAX), 1, MAX+1][rand(0, 2)],
	        "issue_id"=>rand(1, 3),
	        //"type"=>["separated", "group"][rand(0, 1)],
					"deleted"=>[true, false][rand(0, 1)],
	        "created_at"=>Util::ms()-3000000-($i*$j*1000),
	        "updated_at"=>Util::ms()-3000000-($i*$j*1100)
	      ];
	      $db->insert("messages", $message);
			}

    }
	}
}
