  <footer>
  	<a href="#"><img src="/assets/svg/logo.svg" /></a>
    <span>COPYRIGHT © Skill shared ALL RIGHTS RESERVED.</span>
  </footer>
  <!-- //footer -->

  <div id="fb-root"></div>

  <!-- honeybase -->
  <script src="http://connect.facebook.net/en_US/sdk.js"></script>
  <script src="https://d1fxtkz8shb9d2.cloudfront.net/websocket-multiplex-0.1.js"></script>
  <script src="http://cdn.jsdelivr.net/sockjs/0.3.4/sockjs.min.js"></script>
  <script src="/assets/js/honeybase.http.js"></script>
  <!-- honeybase -->

  <script src="/assets/js/chance.js"></script>
  <script src="/assets/js/load_honeybase.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
  <script>
    $(function(){
      if( AI.params().success_flag == null ) ;
      else if (AI.params().success_flag) Notice.success("Successed!");
      else if (!AI.params().success_flag) Notice.error("Error occured.");
    });

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-47711863-11', 'auto');
  ga('send', 'pageview');

  </script>

  <?php include __PUBLIC__ . 'assets/notification.php'; ?>
  <?php include __PUBLIC__ . 'assets/login_modal.php'; ?>

  </body>
</html>
