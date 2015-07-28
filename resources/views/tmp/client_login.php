<?php include __PUBLIC__ . '/assets/header.php' ?>
  <h2>CLIENT LOGIN</h2>
  <input name="title" />
  <textarea name="body" ></textarea>
  <button id="not_loggedin_post">login</button>

  <script>
    (function(global){
      function ASK_TROUBLE(){}
      ASK_TROUBLE.clickLoginListener = function(current_user){
        $('#not_loggedin_post').click(function(e){
          honeybase.auth('facebook', {description: "", type:"client"}, function(flag, user){
            if(flag) {
              if(user) current_user = user;
              var $title = $("input[name=title]");
              var $body = $("textarea[name=body]");
              IssueDB.insert({title:AI.escape($title.val()),body:AI.escape($body.val()), user_id: current_user.id}, function(flag, issue){
                if(flag) AI.redirect('/client');
                else honeybase.logout();
              });
            } else Notice.error("Login canceled");
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
