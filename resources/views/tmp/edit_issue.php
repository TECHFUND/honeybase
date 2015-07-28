<?php include __PUBLIC__ . '/assets/header.php' ?>
  <h2>TROUBLE</h2>


  <h1><?php echo $issue['title']; ?></h1>
  <p><?php echo $issue['body']; ?>
  </br>
  </br>

  <p>いい感じにちょいちょいとチャットの内容をコメントとして表示したりする
  </br>
  <?php
    if($current_user == null){
      echo '<button id="login">専門家としてログインしてコメントする</button>';// type:clientの人がログインするとバグる
    } else {
      if($current_user['type'] == 'expert'){
        echo '<input type="text" name="comment" />';
      } else {
        echo '<a href="/client_login">あなたもマーケティングについて質問する</a>';
      }
    }
  ?>
  </br>

  <script>
    (function(global){
      function TROUBLE(){}
      TROUBLE.issue_id = (function(){ var arr = location.pathname.split("/"); return arr.pop(-1); }());

      TROUBLE.expertLoginListener = function(){
        $("#login").click(function(e){
          honeybase.auth("facebook", {description:""}, function(flag, user){
            if(flag) AI.redirect("/expert/issues/"+TROUBLE.issue_id+"/chat");
            else Notice.error("Login failed");
          });
        });
      }

      TROUBLE.expertCommentListener = function(current_user){
        $("input[name=comment]").keypress(function(e){
          var $self = $(this);
          if(e.which == 13){
            IssueDB.select({id:TROUBLE.issue_id}).done(function(flag, issues){
              MessageDB.insert({body:AI.escape($self.val()), sender_id:current_user.id, receiver_id:issues[0].user_id, issue_id:TROUBLE.issue_id, })
            });
          }
        });
      }

      global.TROUBLE = TROUBLE;
      return global;
    }(window));

    $(function(){
      honeybase.current_user(function(flag, current_user){
        if(flag) TROUBLE.expertCommentListener(current_user);
        else TROUBLE.expertLoginListener();
      });
    });

  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
