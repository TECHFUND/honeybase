<?php include __PUBLIC__ . '/assets/header.php'; ?>
  <h2 style="margin-top: 300px;">本登録完了</h2>
  <?php
    if( isset($current_user) ){
      if ($current_user['type'] == "client"){
        echo '<a href="/client/issues/new">新しいお悩みを相談してみましょう</a>';
      } else if ($current_user['type'] == "expert"){
        echo '<a href="/expert/new">プロフィールを埋めてください</a>';
      } else {
        echo 'なんかおかしい1';
      }
    } else {
      echo 'なんかおかしい2';
    }
  ?>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
