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

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookJavaScriptLoginHelper;

class TutorialController extends Controller {

  public function choose(Request $request) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    if($current_user != null){
      $teams = Team::all();
      return view('my.profile.choose', ["current_user"=>$current_user, "teams"=>$teams]);
    } else {
      return redirect('/');
    }
  }
}
