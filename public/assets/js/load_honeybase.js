/***********************************
 * SUPER DUPER CONVINIENT VARS&FUNCS
 ***********************************/
(function(global){
  global.honeybase = new HoneyBase(location.protocol + "//" + location.host);

  global.UserDB = honeybase.db("users");
  global.ExpertDB = honeybase.db("experts");
  global.UserIssueDB = honeybase.db("user_issues");

  global.MessageDB = honeybase.db("messages");

  global.SkillDB = honeybase.db("skills");
  global.ExpertSkillDB = honeybase.db("expert_skills");

  global.IssueDB = honeybase.db("issues");
  global.IssueExpertDB = honeybase.db("issue_experts");
  global.IssueDiscusserDB = honeybase.db("issue_discussers");
  global.IssueExecutorDB = honeybase.db("issue_executors");

  global.NotificationDB = honeybase.db("notifications");

  global.Uploader = honeybase.uploader("image");
  global.chance = new Chance();

  function AI (){}
  AI.renderUserList = function (datum){
    $("#users").append('');
  }
  AI.rand = function(a, b){
    return a + Math.floor( Math.random() * (b - a + 1) );
  }
  AI.clickedID = function(e){
    var target_id_str = $(e.target.parentNode.parentNode).find("._id").text();
    var id = parseFloat(target_id_str.replace(',',''));
    return id;
  }
  AI.afterReload = function(flag){
    if(flag) location.reload();
    else Notice.error("通信エラー");;
  }
  AI.redirect = function(path){
    location.href = path;
  }
  AI.reload = function(params){
    if(params) {
      window.location.href = window.location.href + "?" + AI.querystring(params);
    } else location.reload();
  }
  AI.querystring = function (params) {
		var params_array = []
		for(var key in params) {
      if(typeof params[key] == "number") params_array.push(key + "=" + encodeURIComponent(params[key]));
      if(typeof params[key] == "string") params_array.push(key + "=" + encodeURIComponent(params[key]));
      if(typeof params[key] == "object") params_array.push(key+"="+JSON.stringify(params[key]));
      if(typeof params[key] == "boolean") params_array.push(key+"="+params[key]);
		}
		return params_array.join("&");
	}
  AI.params = function(){
    var params = {};
    var amp_separated = location.search.substr(1).split("&");
    res = amp_separated.map(function (item) {
      tmp = item.split("=");
      params[tmp[0]] = decodeURIComponent(tmp[1]);
      return params;
    });
    return res[res.length-1];
  }


  AI.escape = (function (String) {
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
  AI.httpEscape = function(text){
    return encodeURIComponent(text.replace(/\n/g, "</br>"));
  }
  AI.decode = function(it){
    return decodeURIComponent(it);
  }

  function Time(){}
  Time.jp = {};
  Time.japan_now = function(){
    var myTbl=new Array("日","月","火","水","木","金","土");
    return Time.japanize(new Date());
  }
  Time.japanize = function(utc){
    if(utc != null){
      var myTbl=new Array("日","月","火","水","木","金","土");
      var myD=utc;
      var myYear=myD.getYear();
      var myYear4=(myYear < 2000) ? myYear+1900 : myYear;
      var myMonth=myD.getMonth() + 1;
      var myDate=myD.getDate();
      var myDay=myD.getDay();
      var myHours=myD.getHours();
      var myMinutes=myD.getMinutes();
      var mySeconds=myD.getSeconds();
      var myMess1=myYear4 + "年" + myMonth + "月" + myDate + "日";
      var myMess2=myTbl[myDay] + "曜日";
      var myMess3=myHours + "時" + myMinutes + "分";
      var myMess=myMess1 + " " + myMess2 + " " + myMess3;
      return myMess;
    } else {
      return "";
    }
  }
  Time.ms2str = function(ms){
    ms = (typeof ms == "string") ? parseInt(ms) : ms;
    var d = new Date(ms);
    return d.getFullYear()+"-"+(d.getMonth()+1)+"-"+d.getDate()+" "+d.getHours()+":"+('0'+d.getMinutes()).slice( -2 );
  }
  Time.to_date = function(str){
    if(str){
      var ms = Date.parse(str);
      return new Date(ms);
    } else {
      return null;
    }
  }
  function User () {}
  User.isLoggedIn = function(cb){
    honeybase.current_user(function(flag, current_user){
      if(flag){
        cb(current_user);
      } else {
        AI.redirect('/');
      }
    });
  }

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
  Notice.pub = function(user_id){
    global.NotificationDB.insert({user_id: expert_id}, function(flag, n){
      if(flag) {
        var pubsub = honeybase.pubsub("notification"+user);
        pubsub.pub({});
      } else Notice.error("通信エラー");
    });
  }

  global.Notice = Notice;
  global.User = User;
  global.Time = Time;
  global.AI = AI;


  return global;
}(window));
