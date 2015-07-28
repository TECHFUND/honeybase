<?php

use Illuminate\Database\Seeder;
use App\Models\MysqlAdaptor;
use App\Models\User;
use App\Models\Team;
use App\Models\Feed;
use App\Models\Message;
use App\Util\NuLog;
use App\Util\Util;

class ProductionSeeder extends Seeder {

	public function run() {
    $_data = App\Util\Util::getJSON(__CONFIG__.'/seed.json');
    $teams_seed = $_data->teams;
    $events_seed = $_data->events;
    $questions_seed = $_data->questions;
    $answers_seed = $_data->answers;

    $db = new MysqlAdaptor();

    foreach($teams_seed as $team){
      $team = (array) $team;
      $team['created_at'] = Util::ms();
      $team['updated_at'] = Util::ms();
      $db->insert("teams", $team);
    }

    foreach($events_seed as $event){
      $event = (array) $event;
      $event['created_at'] = Util::ms();
      $event['updated_at'] = Util::ms();
      $event['description'] = implode("\n", $event['description']);

      $db->insert("events", $event);
    }

    foreach($questions_seed as $question){
      $question = (array) $question;
      $question['created_at'] = Util::ms();
      $question['updated_at'] = Util::ms();
      $db->insert("questions", $question);
    }

    foreach($answers_seed as $answer){
      $answer = (array) $answer;
      $answer['created_at'] = Util::ms();
      $answer['updated_at'] = Util::ms();
      $db->insert("answers", $answer);
    }
	}
}
