<?php include __PUBLIC__ . '/assets/header.php' ?>
<?php
  function exploit_expert($messsages, $current_user){
    if (count($messsages) > 0) {
      $message = $messsages[0];
      if($current_user['id'] == $message['sender_id']){
        return $message['receiver_id'];
      } elseif ($current_user['id'] == $message['receiver_id']) {
        return $message['sender_id'];
      } else {
        return null;
      }
    } else {
      return null;
    }
  }
?>
  <h2>CHAT with EXPERTS</h2>
  <a href="/client/issues/1/group_chat">executors chat</a></br>
  <button id="choose">choose</button><?php echo
  '<div class="chatbox">'; foreach($messages_list as $messages){ $expert_id = exploit_expert($messages, $current_user); echo
    '<div id="expert-'.$expert_id.'">'.
      '<input type="checkbox" name="checkbox" data-expertid="'.$expert_id.'" />'.
      '<ul>'; foreach($messages as $message){ echo '
        <li id="'.$message['id'].'">'.
          '<img src="'.$message['picture'].'" style="width:50px;" />'.
          '</br>'.
          $message['full_name'].': '.$message['body'].
        '</li>'; } echo
      '</ul>'.
      '<input name"chat" id="chatinput'.$expert_id.'" />'.
    '</div>'; }; echo
  '</div>'; ?>


  <style>
    .chatbox {
      display: flex;
      flex-direction: row;
      align-items: flex-start;
    }
  </style>

  <script>
    (function(global){
      function CHATS(){}
      CHATS.issue_id = (function(){ var arr = location.pathname.split("/"); arr.pop(-1); return arr.pop(-1); }());

      CHATS.append = function(data, current_user, expert_id){
        var id = data.id;
        var value = data.value;
        $("#expert-"+expert_id).append('<li id="'+id+'" style="display:none;"><img src='+current_user.picture+' style="width:50px;" /></br>'+current_user.full_name+': '+value.body+'</li>')
        $("#"+id).show('slow');
      }

      CHATS.submit = function(current_user, expert_id, pubsub, cb){
        $("#chatinput"+expert_id).keypress(function(e){
          var $self = $(this);
          if (e.which == 13) {
            var body = $self.val();
            IssueDB.select({id:CHATS.issue_id}).done(function(flag, issues){
              if(flag) {
                MessageDB.insert({body:body, sender_id:current_user.id, receiver_id:expert_id, issue_id:CHATS.issue_id },function(flag, data){
                  if(flag) {
                    var d = {user:current_user, message:data};
                    pubsub.pub(d, function(){
                      if(cb) cb(data)
                    });
                  }
                  else if(cb) cb(null);
                });
              } else if(cb) cb(null);
            });
          }
        });
      }
      CHATS.chooseExecutors = function(){
        $('#choose').click(function(e){
          var ids = $("input[name=checkbox]:checked").map(function(i, el){
            var checked_expert_id = $(el).data("expertid");
            return checked_expert_id;
          });

          if(ids.length > 0){
            ids.map(function(i, id){
              IssueExecutorDB.insert({executor_id:id, issue_id:CHATS.issue_id},function(flag, data){
                if(flag && i == ids.length-1) Notice.success("Executor registered");
              });
            });
          }
          // idsをexecutorとして登録する
          // eachしてissue_executorsにinsertする
        });
      }

      global.CHATS = CHATS;
      return global;
    }(window));

    $(function(global){
      // pubsubがglobalになってない
      honeybase.current_user(function(flag, current_user){
        IssueExpertDB.select({issue_id: CHATS.issue_id}).done(function(flag, res){
          res.map(function(d){
            var pubsub = honeybase.pubsub("chat-"+CHATS.issue_id+"-"+d.expert_id);
            pubsub.sub(function(data){
              CHATS.append(data.message, data.user, d.expert_id);
            });
            CHATS.submit(current_user, d.expert_id, pubsub);
          });
        });

        CHATS.chooseExecutors();
      });

    });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
