<?php include __PUBLIC__ . '/assets/header.php' ?>
  <h2 style="margin-top:100px;">analytics<h2>
  <ul>
    <li>user_count: <?php echo $counts['user_count']; ?></li>
    <li>client_count: <?php echo $counts['client_count']; ?></li>
    <li>expert_count: <?php echo $counts['expert_count']; ?></li>
    <li>issue_count: <?php echo $counts['issue_count']; ?></li>
  </ul>

  <input type='text' name="full_name" />
  <input type='text' name="email" />
  <button>new user</button>

  <script>
    $(function(){
      $('button').click(function(e){
        var params = {
          "full_name":$('input[name=full_name]').val(),
  				"description":"",
          "email":$("input[name=email]").val(),
          "picture":"",
          "type":"expert",
  				"banned":false,
          "social_id":"",
          "facebook_link":"",
          "user_access_token":"",
          "created_at":Date.now(),
          "updated_at":Date.now(),
  	      'encrypted_password':'test',
  	      'salt':'<?php echo SALT; ?>',
  	      'email_verify':true,
  	      'email_verify_code':'xxxxxxxxxxxxxxxxxxxxxxxxxxxx'
        };
        UserDB.insert(params, function(flag, user){
          if(flag) Notice.success("作成完了");
          else Notice.erro("通信エラー");
        });
      });
    });
  </script>

<?php include __PUBLIC__ . '/assets/footer.php'; ?>
