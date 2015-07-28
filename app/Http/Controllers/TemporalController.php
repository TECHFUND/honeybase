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

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

class TemporalController extends Controller {

  public function index(Request $request) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $issues = Issue::all_with_user();
    return view('tmp.index', ["current_user"=>$current_user, "issues"=>$issues]);
  }

  public function client_login(Request $request) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    if($current_user == null){
      return view('tmp.client_login', ["current_user"=>$current_user]);
    } else {
      return redirect('/'.$current_user['type']);
    }
  }
  public function expert_login(Request $request) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    if($current_user == null){
      return view('tmp.expert_login', ["current_user"=>$current_user]);
    } else {
      return redirect('/'.$current_user['type']);
    }
  }


  public function user(Request $request, $user_id) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $user = User::find($user_id);
    return view('tmp.user', ["current_user"=>$current_user, "user"=>$user]);
  }

}
