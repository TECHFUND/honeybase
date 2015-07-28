<?php include __PUBLIC__ . '/assets/header.php' ?>
  <h2>YOUR ASKING TROUBLES</h2>
  <ul><?php foreach($issues as $issue){ echo
    '<li><a href="/client/issues/'.$issue['id'].'/chat">'.$issue['title'].'</a></li>'; } ?>
  </ul>

  <input name="title" />
  <textarea name="body" ></textarea>
  <button id="post">ask</button>

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
