<?php include __PUBLIC__ . '/assets/header.php' ?>
<?php
  function edit_link(){
    if(isset($current_user)){
      if($current_user['id'] == $user['id']){
        if($current_user['type'] == 'expert'){
          return '/expert/edit';
        } else {
          return '/client/edit';
        }
      }
    } else {
      return '#';
    }
  }
?>
  <h2><?php echo $user['full_name']; ?></h2>
  <p><?php echo $user['email']; ?>
  <a href="<?php echo edit_link(); ?>">Edit</a>

  <script>
    (function(global){
      return global;
    }(window));

  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
