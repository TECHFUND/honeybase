(function(global){
  function IndexController(){}

  IndexController.run = function(){
    $$(function(flag, current_user){
      console.log(flag, current_user);
    })
  }

  IndexController.run();
}(window));
