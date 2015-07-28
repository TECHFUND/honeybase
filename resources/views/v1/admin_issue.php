<?php include __PUBLIC__ . '/assets/header.php' ?>

  <h2 style="margin-top:200px;"><?php echo $issue['title']; ?></h2>
  <a href="/admin/issues/<?php echo $issue['id']; ?>/discussion">discussion</a>
  <p><?php echo $issue['body']; ?></p>
  <p><?php echo $issue['category1']; ?></p>
  <p><?php echo $issue['category2']; ?></p>


<h1 style="margin-top: 30px;">すでに推薦された専門家</h1>
<ul><?php foreach($experts as $expert) { echo
'<li data-n="'.$expert['id'].'"><a href="/admin/issues/'.$expert['id'].'/experts">'.$expert['full_name'].'</a><button class="mail">mail</button><button class="cross">x</button></li>'; } ?>
</ul>


<h1 style="margin-top: 30px;">推薦可能な専門家</h1>
<ul><?php foreach($not_enrolled_experts as $not_enrolled_expert) { echo
'<li data-n="'.$not_enrolled_expert['id'].'"><a href="/admin/issues/'.$not_enrolled_expert['id'].'/experts">'.$not_enrolled_expert['full_name'].'</a><button class="mail">mail</button></li>'; } ?>
</ul>


  <script>
  (function(global){
    function Admin(){}
    Admin.issue_id = (function(){ var arr = location.pathname.split("/"); return arr.pop(-1); }());
    global.Admin = Admin;
    return global;
  }(window));

  $(function(){
    honeybase.current_user(function(flag, current_user){
      console.log(flag, current_user);
    });

    $(".cross").click(function(e){
      var expert_id = $(this).parent().data('n');
      IssueExpertDB.select({expert_id:expert_id, issue_id: Admin.issue_id}).done(function(flag, issue_expert){
        if(flag && issue_expert.length > 0) {
          IssueExpertDB.delete(parseInt(issue_expert[0].id), function(flag, issue_expert){
            if(flag) Notice.success("expertアサイン取り消し");
            else Notice.error("通信エラー");
          });
        } else Notice.error("通信エラー");
      });
    });

    $('.mail').click(function(e){
      var expert_id = $(this).parent().data('n');
      IssueExpertDB.insert({issue_id: Admin.issue_id, expert_id: expert_id}, function(flag, issue_expert){
        if(flag){
          IssueDB.select({id:Admin.issue_id}).done(function(flag, issue){
            if(flag) {
              var client_id = issue[0].user_id;

              Notice.pub(client_id); // ディスカッションメンバーとして推薦が追加されたことの通知
              Notice.pub(expert_id); // ディスカッションメンバーとして推薦されたことの通知

              honeybase.ajax("GET", "/admin/send_mail", {issue_id: Admin.issue_id, expert_id:expert_id}, function(res){
                if(res.flag) Notice.success("Success to send mail");
                else Notice.error("Failed to send mail");
              });
            } else Notice.error("Failed to search issue");
          });
        } else {
          Notice.error("通信エラー");
        }
      });
    });
  });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
