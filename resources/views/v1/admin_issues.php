<?php include __PUBLIC__ . '/assets/header.php' ?>

  <h2 style="margin-top:200px;">admin</h2>
  <ul><?php foreach($issues as $issue) { echo
    '<li><a href="/admin/issues/'.$issue['id'].'">'.$issue['title'].'</a></li>' ; } ?>
  </ul>

  <script>
    (function(global){
      function _A(){}
      _A._a = function(){
      }

      global._A = _A;
      return global;
    }(window));

    $(function(){
      honeybase.current_user(function(flag, current_user){
      });
    });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
