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

class IssueController extends Controller {

  public function show(Request $request, $issue_id) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $issue = Issue::find($issue_id);

    $db = new MysqlAdaptor();
    $sql = "SELECT m.*, u.full_name, u.picture FROM messages AS m ".
      "INNER JOIN users AS u ON m.sender_id = u.id ".
      "WHERE m.issue_id = ".$issue_id." ".
      "ORDER BY m.id ASC LIMIT 5";
    $comments = $db->sql($sql)['data'];

    return view('tmp.issue', ["current_user"=>$current_user, "issue"=>$issue, "comments"=>$comments]);
  }

  public function edit(Request $request, $issue_id) {
    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $issue = Issue::find($issue_id);
    return view('tmp.edit_issue', ["current_user"=>$current_user, "issue"=>$issue]);
  }


}
