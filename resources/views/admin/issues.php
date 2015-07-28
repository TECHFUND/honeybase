<?php include __PUBLIC__ . 'assets/header.php' ?>

<h1 style="margin-top: 300px;">issues</h1>

<ul><?php foreach($issues as $issue) { echo
'<li><a href="/admin/issues/'.$issue['id'].'/experts">'.$issue['title'].'</a></li>'; } ?>
</ul>


<script>
  (function(global){
    function Admin(){}
    global.Admin = Admin;
    return global;
  }(window));

  $(function(){
    $("button").click(function(e){
      honeybase.auth("facebook", {description:""}, function(flag, user){

      });
    });

    honeybase.current_user(function(flag, current_user){
      console.log(flag, current_user);
    });
  });
</script>

<?php include __PUBLIC__ . 'assets/footer.php'; ?>
