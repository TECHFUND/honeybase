<?php include __PUBLIC__ . 'assets/header.php' ?>

<h1 style="margin-top: 300px;">enrolled experts</h1>

<ul><?php foreach($experts as $expert) { echo
'<li data-n="'.$expert['id'].'"><a href="/admin/issues/'.$expert['id'].'/experts">'.$expert['full_name'].'</a><button>mail</button></li>'; } ?>
</ul>


<h1 style="margin-top: 30px;">not enrolled experts</h1>
<ul><?php foreach($not_enrolled_experts as $not_enrolled_expert) { echo
'<li data-n="'.$not_enrolled_expert['id'].'"><a href="/admin/issues/'.$not_enrolled_expert['id'].'/experts">'.$not_enrolled_expert['full_name'].'</a><button>mail</button></li>'; } ?>
</ul>


<script>
  (function(global){
    function Admin(){}
    Admin.issue_id = (function(){ var arr = location.pathname.split("/"); arr.pop(-1); return arr.pop(-1); }());
    global.Admin = Admin;
    return global;
  }(window));

  $(function(){
    honeybase.current_user(function(flag, current_user){
      console.log(flag, current_user);
    });

    $('button').click(function(e){
      var expert_id = $(this).parent().data('n');
      honeybase.ajax("GET", "/admin/send_mail", {issue_id: Admin.issue_id, expert_id:expert_id}, function(res){
        if(res.flag) Notice.success("Success to send mail");
        else Notice.error("Failed to send mail");
      });
    });
  });
</script>

<?php include __PUBLIC__ . 'assets/footer.php'; ?>
