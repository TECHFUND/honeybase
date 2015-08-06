/***********************************
 * SUPER DUPER CONVINIENT VARS&FUNCS
 ***********************************/
(function(global){
  global.honeybase = new HoneyBase(location.protocol + "//" + location.host);


  // jQuery-like login check helper
  var $$ = function(cb){
    global.addEventListener("DOMContentLoaded", function(){
      global.honeybase.current_user(function(flag, current_user){
        cb(flag, current_user);
      });
    });
  }
  global.$$ = $$;

  return global;
}(window));
