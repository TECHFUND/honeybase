<?php include __PUBLIC__ . 'assets/header.php' ?>
<?php
  function render_state($message){
    $res = "";
    if ($message['deleted']) {
      $res = "解除";
    } else {
      $res = "削除";
    }
    return $res;
  }
?>


<h1 style="margin-top: 300px;"></h1>

<ul><?php foreach($messages as $message){ echo
  '<li data-n="'.$message['id'].'"><img style="width:40px;" src="'.$message['picture'].'" />'.
    '<a href=/users/"'.$message['sender_id'].'">'.$message['full_name'].'</a>'.
    '<p>'.$message['body']." - ".$message['created_at'].'</p>'.
    '<button>'.render_state($message).'</button>'.
  '</li>';} ?>
</ul>

<script>
  $(function(){
    $('button').click(function(e){
      var $self = $(this);
      var message_id = $self.parent().data('n')
      MessageDB.select({id: message_id}).done(function(flag, messages){
        if(flag) {
          var message = messages[0];
          MessageDB.update(parseInt(message.id), {banned: !message.banned}, function(flag, message){
            if(flag) AI.reload({success_flag:true});
            else Notice.error("failed to update");
          })
        } else Notice.error("failed to select");
      });
    });
  });
</script>

<?php include __PUBLIC__ . 'assets/footer.php'; ?>
