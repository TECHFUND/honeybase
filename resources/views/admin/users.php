<?php include __PUBLIC__ . 'assets/header.php' ?>
<?php
  function render_state($user){
    $res = "";
    if ($user['banned']) {
      $res ="解除";
    } else {
      $res = "凍結";
    }
    return $res;
  }
?>

<h1 style="margin-top: 300px;">Users</h1>

<ul><?php foreach($users as $user){ echo
  '<li data-n="'.$user['id'].'">'.
    '<a href="/users/'.$user['id'].'">'.$user['full_name'].' - '.$user['created_at'].'</a>'.
    '<button>'.render_state($user).'</button>'.
  '</li>';} ?>
</ul>

<script>
  $(function(){
    $('button').click(function(e){
      var $self = $(this);
      var user_id = $self.parent().data('n');
      UserDB.select({id: user_id}).done(function(flag, users){
        if(flag) {
          var user = users[0];
          // banned: falseで保存するとエラーになる。falseがPHP上で空白文字に変換されている
          UserDB.update(parseInt(user.id), {banned: !user.banned}, function(flag, user){
            if(flag) AI.reload({success_flag:true});
            else Notice.error("failed to update");
          });
        } else Notice.error("failed to select");
      });
    });
  });
</script>

<?php include __PUBLIC__ . 'assets/footer.php'; ?>
