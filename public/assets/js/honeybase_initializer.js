/***********************************
 * SUPER DUPER CONVINIENT VARS&FUNCS
 ***********************************/
(function(global){
  global.honeybase = new HoneyBase(location.protocol + "//" + location.host);


  // jQuery-like login check helper
  var $$ = function(cb){
    global.honeybase.current_user(function(flag, current_user){
      document.addEventListener('DOMContentLoaded', function(){
        cb(flag, current_user);
      }, false);
    });
  }
  global.$$ = $$;

  return global;
}(window));
