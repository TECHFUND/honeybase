<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MysqlAdaptor;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Log;
use App\Util\NuLog;
use App\Util\Mail;
use App\Models\User;
use App\Models\Admin;
use App\Models\Team;
use App\Models\Feed;
use App\Models\Message;
use App\Models\Issue;

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

class AdminController extends Controller {

  public function index(Request $request) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    return view('admin.index', ["current_user"=>$current_user]);
  }

  public function issues(Request $request) {
    $issues = Issue::all_with_user();
    return view('admin.issues', ["current_user"=>$request->current_user, "issues"=>$issues]);
  }

  public function issue_experts(Request $request, $issue_id) {
    $experts = Issue::experts($issue_id);
    $not_enrolled_experts = Issue::not_enrolled_experts($issue_id);
    return view('admin.issue_experts', ["current_user"=>$request->current_user, "experts"=>$experts, "not_enrolled_experts"=>$not_enrolled_experts]);
  }

  public function send_mail(Request $request) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $headers = ['Access-Control-Allow-Origin' => ORIGIN, "Set-Cookie"=>SERVICE_NAME."id"."=".$session_id."; path=/", "Access-Control-Allow-Credentials"=>"true"];
    $data = $request->all();
    $issue_id = $data['issue_id'];
    $expert_id = $data['expert_id'];
    $res = Mail::recommendation_mail($issue_id, $expert_id);
    return response(["flag"=>$res], 200, $headers);
  }

  public function chats(Request $request) {
    $messages = Message::all_with_user(); // limitいるわ
    return view('admin.chats', ["current_user"=>$request->current_user, "messages"=>$messages]);
  }

  public function users(Request $request) {
    $users = User::all();
    return view('admin.users', ["current_user"=>$request->current_user, "users"=>$users]);
  }
}
