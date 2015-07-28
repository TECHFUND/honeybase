<?php include __PUBLIC__ . '/assets/header.php' ?>
  <h2>LOGIN</h2>
  <a href="/expert_login" style="float:right;">専門家はコチラ</a>

  <ul><?php foreach($issues as $issue){ echo
    '<li><img style="width:40px" src="'.$issue['picture'].'" /><a href="users/'.$issue['user_id'].'">'.$issue['full_name'].'</a></br>'.
      '<a href="/issues/'.$issue['id'].'">'.$issue['title'].'</a>'.
    '</li>'; } ?>
  </ul>

  </br>
  <a href="/client_login">質問する</a>

  <input type='text' name='email' />
  <input type='password' name='password' />

  <script>
    $(function(){
      $("input[name=password]").keypress(function(e){
        if(e.which == 13) {
          var email = $("input[name=email]").val();
          var password = $("input[name=password]").val();
          honeybase.signup(email, password, {type:"client"}, function(flag, user){
            console.log(flag, user);
          });
        }
      });

    });

  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
