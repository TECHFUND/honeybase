<?php include __PUBLIC__ . '/assets/header.php' ?>
  <h2>GROUP CHAT</h2>


  <ul id="chat"><?php foreach($messages as $message){ $color = ($message['sender_id']==$current_user['id']) ? 'style="color:red;"' : ""; echo '
    <li id="'.$message['id'].'" '.$color.'>'.
      '<img src="'.$message['picture'].'" style="width:50px;" />'.
      $message['full_name'].': '.$message['body'].
    '</li>'; } ?>
  </ul>

  <input name="chat" />

  <script>
    (function(global){
      function GROUP_CHAT(){}
      GROUP_CHAT.issue_id = (function(){ var arr = location.pathname.split("/"); arr.pop(-1); return arr.pop(-1); }());

      GROUP_CHAT.append = function(data, user){
        var id = data.id;
        var value = data.value;
        $("#chat").append('<li id="'+id+'" style="display:none;"><img src='+user.picture+' style="width:50px;" /></br>'+user.full_name+': '+value.body+'</li>')
        $("#"+id).show('slow');
      }

      GROUP_CHAT.submit = function(current_user, pubsub, cb){
        $("input[name=chat]").keypress(function(e){
          var $self = $(this);
          if (e.which == 13) {
            var body = $self.val();
            MessageDB.insert({body:body, sender_id:current_user.id, issue_id:GROUP_CHAT.issue_id },function(flag, data){
              if(flag) {
                var d = {user:current_user, message:data};
                pubsub.pub(d, function(){
                  if(cb) cb(data);
                });
              }
              else if(cb) cb(null);
            });
          }
        })

      }

      global.GROUP_CHAT = GROUP_CHAT;
      return global;
    }(window));

    $(function(global){
      var pubsub = honeybase.pubsub("group_chat"+GROUP_CHAT.issue_id);
      honeybase.current_user(function(flag, current_user){
        pubsub.sub(function(data){
          GROUP_CHAT.append(data.message, data.user);
        });

        GROUP_CHAT.submit(current_user, pubsub);
      });

    });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
