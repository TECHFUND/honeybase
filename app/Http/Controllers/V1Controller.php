<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MyAdaptor;
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
use App\Models\MysqlAdaptor;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

class V1Controller extends Controller {

  /* all */
  public function index(Request $request) {
    return view('v1.index');
  }

  public function about(Request $request) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);

    return view('v1.about', ["current_user"=>$current_user]);
  }


  public function discussion(Request $request, $issue_id) {
    // $current_user = $request->current_user;
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);

    $issue = Issue::find($issue_id);

    $issue_discussers = (new MysqlAdaptor())->select("issue_discussers", ["issue_id"=>$issue['id']])['data'];
    $discussers = [];
    foreach($issue_discussers as $issue_discusser){
      array_push($discussers, User::find($issue_discusser['discusser_id']) );
    }

    $messages = Message::group_history($issue['id']);
    return view('v1.discussion', ["current_user"=>$current_user, "issue"=>$issue, "discussers"=>$discussers, "messages"=>$messages]);
  }

  public function to_offline(Request $request, $issue_id) {
    // $current_user = $request->current_user;
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);

    $issue = Issue::find($issue_id);

    $issue_executors = (new MysqlAdaptor())->select("issue_executors", ["issue_id"=>$issue['id']])['data'];
    $executors = [];
    foreach($issue_executors as $issue_executor){
      array_push($executors, User::find($issue_executor['executor_id']) );
    }

    $messages = Message::group_history($issue['id']);
    return view('v1.to_offline', ["current_user"=>$current_user, "issue"=>$issue, "executors"=>$executors, "messages"=>$messages]);
  }

  /* expert */
  public function new_expert(Request $request) {
    // $current_user = $request->current_user;
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    return view('v1.new_expert', ["current_user"=>$current_user]);
  }

  public function waiting_expert(Request $request) {
    // $current_user = $request->current_user;
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    return view('v1.waiting_expert', ["current_user"=>$current_user]);
  }

  public function expert_issues(Request $request){
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
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
    return view('v1.expert_issues', ["current_user"=>$current_user, "issues"=>$issues]);
  }


  /* client */
  public function new_issue(Request $request) {
    // $current_user = $request->current_user;
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    return view('v1.new_issue', ["current_user"=>$current_user]);
  }

  public function client_issue(Request $request, $issue_id) {
    // $current_user = $request->current_user;
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $issue = Issue::find($issue_id);

    // expertsを出す
    $db = new MysqlAdaptor();
    $issue_experts = $db->select("issue_experts", ["issue_id"=>$issue_id])['data'];
    $issue_executors = $db->select("issue_executors", ["issue_id"=>$issue_id])['data'];

    $ids = [];
    foreach($issue_experts as $issue_expert){
      array_push($ids, $issue_expert['expert_id']);
    }
    foreach($issue_executors as $issue_executor){
      array_push($ids, $issue_executor['executor_id']);
    }
    $ids = array_unique($ids);

    $experts = [];
    foreach($ids as $id){
      array_push($experts, User::find($id));
    }

    // ディスカッションが開始してたらdiscussionに飛ばす
    $messages = Message::group_history($issue_id);
    if (0 != count($messages)) {
      return redirect('/issues/' . $issue_id . '/discussion');
    }

    return view("v1.client_issue", ["current_user"=>$current_user, "issue"=>$issue, "experts"=>$experts]);
  }

  public function client_issues(Request $request){
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);

    $issues = Issue::select(["user_id"=>$current_user['id']]);
    return view("v1.client_issues", ["current_user"=>$current_user, "issues"=>$issues]);
  }

  /* admin */
  public function admin_login(Request $request) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);

    return view('v1.admin_login', ["current_user"=>$current_user]);
  }

  public function admin_issues(Request $request) {
    // $current_user = $request->current_user;
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);

    $issues = Issue::all_with_user();
    return view('v1.admin_issues', ["current_user"=>$current_user, "issues"=>$issues]);
  }

  public function admin_issue(Request $request, $issue_id) {
    // $current_user = $request->current_user;
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);

    $issue = Issue::find($issue_id);
    $experts = Issue::experts($issue_id);
    $not_enrolled_experts = Issue::not_enrolled_experts($issue_id);

    return view('v1.admin_issue', ["current_user"=>$current_user, "issue"=>$issue, "experts"=>$experts, "not_enrolled_experts"=>$not_enrolled_experts]);
  }

  public function admin_discussion(Request $request, $issue_id) {
    // $current_user = $request->current_user;
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);

    $issue = Issue::find($issue_id);
    $issue_experts = (new MysqlAdaptor())->select("issue_experts", ["expert_id"=>$issue['id']])['data'];
    $experts = [];
    foreach($issue_experts as $issue_expert){
      array_push($experts, User::find($issue_expert['id']) );
    }

    $messages = Message::group_history($issue['id']);
    return view('v1.admin_discussion', ["current_user"=>$current_user, "issue"=>$issue, "experts"=>$experts, "messages"=>$messages]);
  }

  public function analytics(Request $request){
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);

    $counts = [
      "user_count"=>count( User::all() ),
      "client_count"=>count( User::search(["type"=>"client"]) ),
      "expert_count"=>count( User::search(["type"=>"expert"]) ),
      "issue_count"=>count( Issue::all() )
    ];

    return view('v1.admin_analytics', ["current_user"=>$current_user, "counts"=>$counts]);
  }

  public function admin_usernum(Request $request) {

    // $current_user = $request->current_user;
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);

    //NuLog::info($a, __FILE__, __LINE__);

    $issues = Issue::all_with_user();
    return view('v1.admin_usernum', ["current_user"=>$current_user, "issues"=>$issues]);
  }

}
