(function(global){
  function HoneyCumb (){}
  HoneyCumb.querystring = function (params) {
		var params_array = []
		for(var key in params) {
      if(typeof params[key] == "number") params_array.push(key + "=" + encodeURIComponent(params[key]));
      if(typeof params[key] == "string") params_array.push(key + "=" + encodeURIComponent(params[key]));
      if(typeof params[key] == "object") params_array.push(key+"="+JSON.stringify(params[key]));
      if(typeof params[key] == "boolean") params_array.push(key+"="+params[key]);
		}
		return params_array.join("&");
	}

  HoneyCumb.params = function(){
    var params = {};
    var amp_separated = location.search.substr(1).split("&");
    res = amp_separated.map(function (item) {
      tmp = item.split("=");
      params[tmp[0]] = decodeURIComponent(tmp[1]);
      return params;
    });
    return res[res.length-1];
  }
  HoneyCumb.ready = function(cb){
    global.honeybase.current_user(function(flag, current_user){
      global.onload = function(){
        cb(flag, current_user);
      }
    });
  }


  HoneyCumb.escape = (function (String) {
    var escapeMap = {
      '&': '&amp;',
      "'": '&#x27;',
      '`': '&#x60;',
      '"': '&quot;',
      '<': '&lt;',
      '>': '&gt;'
    };
    var escapeReg = '[';
    var reg;
    for (var p in escapeMap) {
      if (escapeMap.hasOwnProperty(p)) {
        escapeReg += p;
      }
    }
    escapeReg += ']';
    reg = new RegExp(escapeReg, 'g');
    return function escapeHtml (str) {
      str = (str === null || str === undefined) ? '' : '' + str;
      return encodeURIComponent(str.replace(reg, function (match) {
        return escapeMap[match];
      }).replace(/\n/g, "</br>"));
    };
  }(String));

  global.HoneyCumb = HoneyCumb;
  return global;
}(window));
