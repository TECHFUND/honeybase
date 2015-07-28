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

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

class ClientController extends Controller {

  public function issues(Request $request) {
    $current_user = $request->current_user;
    $issues = Issue::select(["user_id"=>$current_user['id']]);

    return view("client.issues", ["current_user"=>$current_user, "issues"=>$issues]);
  }

  public function chat(Request $request, $issue_id){
    $experts = Issue::experts($issue_id);

    $current_user = $request->current_user;
    $messages_list = [];
    foreach($experts as $expert){
      $message = Message::separated_history($expert['id'], $current_user['id']);

      // メッセージがまだないとき、初期値を入れる
      if(count($message) == 0){
        $message = [
          [
            "id"=>0,
            "receiver_id"=>$current_user['id'],
            "sender_id"=>$expert['id'],
            "issue_id"=>$issue_id,
            "type"=>"separated",
            "picture"=>$expert['picture'],
            "full_name"=>$expert['full_name'],
            "body"=>"no comment"
          ]
        ];
      }
      array_push($messages_list, $message);
    }
    return view("client.chat", ["current_user"=>$current_user, "messages_list"=>$messages_list, "experts"=>$experts]);
  }

  public function group_chat(Request $request, $issue_id){
    // アクセス主がexecutorsでなければ弾く
    $messages = Message::group_history($issue_id);
    $current_user = $request->current_user;
    $executors = Issue::executors($issue_id);

    if( Issue::isClient($issue_id, $current_user) ){
      return view("client.group_chat", ["current_user"=>$current_user, "messages"=>$messages, "executors"=>$executors]);
    } else {
      return redirect('/');
    }
  }

  public function edit(Request $request){
    $current_user = $request->current_user;
    return view("client.edit", ["current_user"=>$current_user]);
  }

}
