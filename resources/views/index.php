<?php include __PUBLIC__ . 'assets/header.php' ?>

<script>
  $(function(){
    honeybase.current_user(function(isLoggedIn, current_user){
      if(isLoggedIn) location.href = "/my/feed";
      else location.href = "/lp";
    });
  });
</script>

<?php include __PUBLIC__ . 'assets/footer.php'; ?>
