<?php include __PUBLIC__ . '/assets/header.php' ?>



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