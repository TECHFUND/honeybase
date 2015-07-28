<?php namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Util\NuLog;
use App\Util\Util;
use App\Models\Issue;

class StakeholderMiddleware {

  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  // MiddleWareでinsert, select含めたrequestのuser_idを取得してcurrent_user->idと比較するのはありかも
  // insert, update, delete, selectにaccess: all, none, loginを定義できる
  public function handle($request, Closure $next, $issue_id){

    $session_id = $request->cookie(SERVICE_NAME.'id');
    $current_user = User::current_user($session_id);
    $header = ['Access-Control-Allow-Origin' => ORIGIN, "Access-Control-Allow-Credentials"=>"true"];

    // ログインしてたら続ける
    if($current_user != null){
      $db = new MysqlAdaptor();
      $issue = Issue::find($issue_id);

      $isAdmin = $current_user['type'] == "admin";
      $isOwnerClient = $current_user['id'] == $issue['user_id'];

      $issue_experts = $db->select("issue_experts", ["issue_id"=>$issue_id])['data'];
      $expert_ids = [];
      foreach($issue_experts as $issue_expert){
        $expert_ids += $issue_expert['expert_id'];
      }
      $isExpert = in_array($current_user['id'], $expert_ids, true);

      $issue_executors = $db->select("issue_executors", ["issue_id"=>$issue_id])['data'];
      $executor_ids = [];
      foreach($issue_executors as $issue_executor){
        $executor_ids += $issue_executor['expert_id'];
      }
      $isExecutor = in_array($current_user['id'], $executor_ids, true);

      $isStakeholder = $isOwnerClient || $isExpert || $isExecutor || $isAdmin;

      if($isStakeholder){
        $request->current_user = $current_user;
        return $next($request);
      } else {
        return redirect('/');
        // 本当は「ログインしてください」「ステークホルダー意外は閲覧できません」みたいなviewが欲しい
      }
    } else {
    // ログインしてなかったら戻す
      return redirect('/');
    }
  }

}
