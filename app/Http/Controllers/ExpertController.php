<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MysqlAdaptor;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Log;
use App\Util\NuLog;
use App\Models\User;
use App\Models\Team;
use App\Models\Feed;
use App\Models\Message;
use App\Models\Issue;
use App\Models\Skill;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

class ExpertController extends Controller {

  public function issues(Request $request) {
    $current_user = $request->current_user;
    $db = new MysqlAdaptor();
    $issue_experts = $db->select("issue_experts", ["expert_id"=>$current_user['id']])['data'];
    $issue_executors = $db->select("issue_executors", ["executor_id"=>$current_user['id']])['data'];


    $ids = [];
    foreach($issue_experts as $issue_expert){
      array_push($ids, $issue_expert['issue_id']);
    }
    foreach($issue_executors as $issue_executor){
      array_push($ids, $issue_executor['issue_id']);
    }
    $ids = array_unique($ids);

    $issues = [];
    foreach($ids as $id){
      array_push($issues, Issue::find($id));
    }


    return view("expert.issues", ["current_user"=>$current_user, "issues"=>$issues]);
  }

  public function chat(Request $request, $issue_id){
    $client = Issue::client($issue_id);
    $current_user = $request->current_user;
    $messages = Message::separated_history($client['id'], $current_user['id']);
    return view("expert.chat", ["current_user"=>$current_user, "messages"=>$messages, "client"=>$client]);
  }

  public function group_chat(Request $request, $issue_id){
    $current_user = $request->current_user;
    if( Issue::isExecutor($issue_id, $current_user) ){
      $messages = Message::group_history($issue_id);
      return view("expert.group_chat", ["current_user"=>$current_user, "messages"=>$messages]);
    } else {
      return redirect('/');
    }
  }

  public function edit(Request $request){
    $current_user = $request->current_user;
    $db = new MysqlAdaptor();
    $expert_skills = $db->select("expert_skills", ["expert_id"=>$current_user['id']])['data'];

    $skills = [];
    foreach($expert_skills as $expert_skill){
      array_push($skills, Skill::find($expert_skill['skill_id']));
    }
    return view("expert.edit", ["current_user"=>$current_user, "skills"=>$skills]);
  }

  public function search (Request $request){
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    $data = $request->all();
    $q = (array)json_decode($data['value']);
    $skills = Skill::ambiguous_search($q);
    return response(['flag'=>count($skills) > 0, 'data'=>$skills], 200, $headers);
  }

}
