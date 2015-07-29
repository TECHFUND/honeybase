(function(global){
  function IndexController(){}

  IndexController.run = function(){
    $$(function(flag, current_user){

      console.log(flag, current_user);

      var ps_hoge = honeybase.pubsub('hoge');

      ps_hoge.sub(function(data){
        console.log(data);
      });

      $(document).click(function(){
        ps_hoge.pub({"hoge":"hoge"});
      });

    });
  }

  IndexController.run();
}(window));
