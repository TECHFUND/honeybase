<?php include __PUBLIC__ . '/assets/header.php' ?>
  <h2>CHAT with CLIENT</h2>

  <a href="/expert/issues/1/group_chat">group chat</a>

  <ul id="chat"><?php foreach($messages as $message){ $user = ($message['sender_id']==$current_user['id']) ? $current_user : $client ; echo '
    <li id="'.$message['id'].'">'.
      '<img src="'.$user['picture'].'" style="width:50px;" />'.
      '</br>'.
      $user['full_name'].': '.$message['body'].
    '</li>'; } ?>
  </ul>

  <input name="chat" />

  <script>
    (function(global){
      function CHATS(){}
      CHATS.issue_id = (function(){ var arr = location.pathname.split("/"); arr.pop(-1); return arr.pop(-1); }());

      CHATS.append = function(data, current_user){
        var id = data.id;
        var value = data.value;
        $("#chat").append('<li id="'+id+'" style="display:none;"><img src='+current_user.picture+' style="width:50px;" /></br>'+current_user.full_name+': '+value.body+'</li>')
        $("#"+id).show('slow');
      }

      CHATS.submit = function(current_user, pubsub, cb){
        $("input[name=chat]").keypress(function(e){
          var $self = $(this);
          if (e.which == 13) {
            var body = $self.val();
            IssueDB.select({id:CHATS.issue_id}).done(function(flag, issues){
              if(flag) {
                var client_id = issues[0].user_id;
                MessageDB.insert({body:body, sender_id:current_user.id, receiver_id:client_id, issue_id:CHATS.issue_id },function(flag, data){
                  if(flag) {
                    var d = {user:current_user, message:data};
                    pubsub.pub(d, function(){
                      if(cb) cb(data);
                    });
                  }
                  else if(cb) cb(null);
                });
              } else if(cb) cb(null);
            });
          }
        })

      }

      global.CHATS = CHATS;
      return global;
    }(window));

    $(function(global){
      honeybase.current_user(function(flag, current_user){
        // 複数人chatなので、subscribeするチャンネルはexpert_idごとに違う。expertsオブジェクトにアクセスしたい
        var pubsub = honeybase.pubsub("chat-"+CHATS.issue_id+"-"+current_user.id);
        pubsub.sub(function(data){
          CHATS.append(data.message, data.user);
        });

        CHATS.submit(current_user, pubsub);
      });

    });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
