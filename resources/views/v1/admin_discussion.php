<?php include __PUBLIC__ . '/assets/header.php' ?>

  <h2 style="margin-top:200px;"><?php echo $issue['title']; ?></h2>
  <a href="/admin/issues/<?php echo $issue['id']; ?>/discussion">discussion</a>
  <p><?php echo $issue['body']; ?></p>
  <p><?php echo $issue['category1']; ?></p>
  <p><?php echo $issue['category2']; ?></p>

  <ul><?php foreach($messages as $message) { echo
    '<li data-n="'.$message['id'].'">'.$message['body'].'</li>' ; } ?>
  </ul>


  <script>
    (function(global){
    }(window));

    $(function(){
      honeybase.current_user(function(flag, current_user){
      });
    });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
