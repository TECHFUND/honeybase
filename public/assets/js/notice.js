(function(global){

  function Notice(){}
  Notice.success = function(mes){
    var pdom = '<p id="_success" style="display:none;position: fixed !important;position: absolute;top: 0;left: 0;width: 100%;background-color: #f0fff0;color: #006400; border-bottom:1px solid #006400;font-size:20px;padding: 5px;z-index: 100000;">'+mes+'</p>';
    $('body').prepend(pdom);
    var $success = $("#_success");
    $success.fadeIn(300);
    $success.delay(1700).fadeOut(2000);
  }
  Notice.error = function(mes){
    var pdom = '<p id="_error" style="display:none;position: fixed !important;position: absolute;top: 0;left: 0;width: 100%;background-color: #fff;color: #ff0000; border-bottom:1px solid #ff0000;font-size:20px;padding: 5px;z-index: 100000;">'+mes+'</p>';
    $('body').prepend(pdom);
    var $error = $("#_error");
    $error.fadeIn(300);
    $error.delay(1700).fadeOut(2000);
  }

  global.Notice = Notice;
  return global;
}(window));
