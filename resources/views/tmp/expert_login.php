<?php include __PUBLIC__ . '/assets/header.php' ?>
  <h2>EXPERT LOGIN</h2>
  <button id="login">login</button>

  <script>
    (function(global){
      return global;
    }(window));

    (function(global){
      $('#login').click(function(e){
        honeybase.auth('facebook', {description: "", type:"expert"}, function(flag, user){
          if(flag) AI.redirect('/expert');
          else Notice.error("Login canceled");
        })
      });

      return global;
    }(window));
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
