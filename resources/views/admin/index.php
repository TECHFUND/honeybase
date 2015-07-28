<?php include __PUBLIC__ . 'assets/header.php' ?>

<h1 style="margin-top: 300px;">admin</h1>
<button>admin login</button>

<ul>
  <li><a href="/admin/issues">依頼管理</a></li>
  <li><a href="/admin/chats">チャット監視</a></li>
  <li><a href="/admin/users">アカウント管理</a></li>
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
