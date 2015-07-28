<?php include __PUBLIC__ . '/assets/header.php' ?>

  <!-- common -->
  <div class="common cf">

  <!----------------------- any contents ----------------------->

  <!-- issuesContents -->
  <article class="issuesContents">


    <!-- issues_home -->
    <h2>アサイン中案件</h2>
  <ul><?php foreach($issues as $issue){ echo
    '<li><a href="/client/issues/'.$issue['id'].'">'.$issue['title'].'</a></li>'; } ?>
  </ul>
  <p>アサイン中の案件はありません</p>

  <!--input name="title" />
  <textarea name="body" ></textarea>
  <button id="post">ask</button-->

  <script>
    (function(global){
      var $title = $("input[name=title]");
      var $body = $("textarea[name=body]");

      function ASK_TROUBLE(){}
      ASK_TROUBLE.clickLoginListener = function(current_user){
        $('#post').click(function(e){
          IssueDB.insert({title:$title.val(), body:$body.val(), user_id: current_user.id}, function(flag, issue){
            if(flag) AI.reload({success_flag: true});
            else Notice.error("Failed to ask issue");
          });
        });
      }

      global.ASK_TROUBLE = ASK_TROUBLE;
      return global;
    }(window));

    $(function(){
      honeybase.current_user(function(flag, current_user){
        ASK_TROUBLE.clickLoginListener(current_user);
      });
    });
  </script>

<?php include __PUBLIC__ . '/assets/footer.php'; ?>
