(function(global){
  var url = location.protocol + "//" +location.host;
  var origin = getConfig("origins.json");
  var env = "development";

  switch(url){
    case origin.development:
      env = "development";
      break;
    case origin.sttaging:
      env = "staging";
      break;
    case origin.production:
      env = "production";
      break;
    default:
      throw new Error("Unknown environment");
  }

  var json = getConfig("honeybase_"+env+"_config.json");
  var pubsub_host = json.pubsub_endpoint;

  var VERSION = "v1";
  var FACEBOOK_CONSUMER_KEY = json.token;

  /* for OAuth */
  FB.init({
    appId      : FACEBOOK_CONSUMER_KEY,
    status     : true,
    xfbml      : true,
    version    : 'v2.3' // or v2.0, v2.1, v2.0
  });


  function getConfig(name){
    $.ajaxSetup({ async: false });
    var data = $.getJSON("http://"+location.host+"/config/"+name, function(){});
    $.ajaxSetup({ async: true });
    return data.responseJSON;
  }

  global.env = env;
  global.VERSION = VERSION;
  global.isTest = false;
  global.testType = "";
  return global;
}(window));



(function(global){

	function HoneyBase(host) {
		this.host = format_host(host);
    this.api = this.host + "api/" + VERSION;
    this.env = env;
	}

	HoneyBase.prototype = {
    /************************************
     * OAUTH
     ************************************/
    auth : function(provider, option, cb){
      if(!cb) {
        cb = option;
        option = {};
      }
			var self = this;
	    FB.getLoginStatus(function(response) {
        if(response.status != "connected"){
  		    FB.login(function(response) {
  		      if (response.authResponse) {
              appLogin(response.authResponse.accessToken, function(flag, user){
                cb(flag, user);
              });
  		      } else {
   		        console.error('User cancelled login or did not fully authorize.');
              cb(false, null);
  		      }
  		    }, {scope: 'public_profile,email,rsvp_event'});
        } else {
          self.current_user(function(flag, current_user){
            if(flag){
              // app鯖でもログインできているか調べて、ログインできてたらcb(true, user)返してスルーさせる
              cb(flag, current_user);
            } else {
              // fbはログインしてるが、app鯖でログインできていない
              appLogin(response.authResponse.accessToken, function(flag, user){
                cb(flag, user);
              });
            }
          });
        }
      });
      function appLogin(accessToken, cb){
        var params = {
          provider: provider,
          user_access_token: accessToken,
          option: JSON.stringify(option)
        };
        $_ajax("POST", self.api+"/auth", params, function(res){
          cb(res.flag, res.user);
        });
      }
    },
		authWithJWT : function(token, cb) {
			var self = this;
			var params = { token : token };
			$_ajax("POST", self.api+"/auth_with_jwt", params, function(data) {
				cb(data.err, data.user);
			});
		},
		signup : function(email, password, option, cb) {
			var self = this;
      if(!cb) {
        cb = option;
        option = {};
      }
      var params = {
        email: email,
        password: password,
        option: JSON.stringify(option)
      };
      $_ajax("POST", self.api+"/signup", params, function(res){
        cb(res.flag, res.user);
      });
    },
		signin : function(email, password, cb) {
			var self = this;
      var params = {
        email: email,
        password: password,
      };
      $_ajax("POST", self.api+"/signin", params, function(res){
        cb(res.flag, res.user);
      });
    },
		logout : function(cb) {
			var self = this;
	    FB.getLoginStatus(function(response) {
        if(response.status == 'connected'){
          setTimeout(function(){
    	      FB.logout(function(response) {
              postLogout(self, cb);
    	      });
          }, 100);
        } else postLogout(self, cb);
      });
      function postLogout(self, cb){
			  $_ajax("POST", self.api + "/logout", {}, function(res) {
          console.log(res.message);
					if(cb) cb(res.flag);
				});
      }
		},
		current_user : function(cb) {
			var self = this;
			var params = {};
	    FB.getLoginStatus(function(response) {
        if(response.status == "connected"){
  				$_ajax("GET", self.api + "/current_user", params, function(data) {
  					if(data.user) cb(true, data.user);
  					else {
              cb(false, null);
            }
          });
        } else {
          cb(false, null);
        }
			});
		},
    search : function(q, cb){
      var self = this;
      var params = {};
      params.value = q;
      params.table = "users";
			$_ajax("GET", self.api + "/db/users/search", params, function(res) {
        cb(res.flag, res.data);
      });
    },
    switchTestState : function(){
      isTest = true;
      this.host = format_host("http://localhost:8001"); // テスト時はローカル鯖8001で動かすのどうなん？
      this.api = this.host + "api/" + VERSION;
    },
    setTestType : function(name){
      testType = name;
    },


/************************************
 * OTHER
 ************************************/
		db : function(table) {
			return new DataBase(table, this.api);
		},
		database : function(table) {
      return this.db(table);
		},
		ping: function(table, cb) {
      if(!cb) cb = table;
      if(!table) table = "";
      $_ajax("GET", this.host+table, {}, cb);
    },
    pubsub: function(channel){
      return new PubSub(channel);
    },
		uploader: function() {
      this._uploader = new Uploader(this.api);
      return this._uploader;
    },
		mailer: function() {
      this._mailer = new Mailer(this.api);
      return this._mailer;
    },
    ajax: function(method, url, params, cb){
      $_ajax(method, url, params, cb);
    }
	}


  /************************************
  * PubSub
  ************************************/
  function PubSub(channel){
    var sockjs_url = pubsub_host;
    var sockjs = new SockJS(sockjs_url);
    var multiplexer = new WebSocketMultiplex(sockjs);
    this.socket = multiplexer.channel('honeybase');
    this.channel = channel;
  }

  PubSub.prototype = {
    publish: function(value, cb){
      this.socket.send(JSON.stringify({channel:this.channel, value: value}));
      if(cb) cb();
    },

    subscribe: function(cb){
      var self = this;

      self.socket.onmessage = function(e) {
        var data = JSON.parse(e.data);
        var channel = data.channel;
        var value = data.value;

        if(self.channel == channel) cb(value);
        else {}
      };
    },
  	pub: function(value, cb) {
      this.publish(value, cb);
  	},
  	send: function(value, cb) {
      this.publish(value, cb);
  	},
    sub: function(cb){
      this.subscribe(cb);
    },
    onsend: function(cb){
      this.subscribe(cb);
    }
  }


/************************************
 * UPLOADER
 ************************************/
	function Uploader(host) {
    this.path = host+"/uploader";
	}

  Uploader.prototype = {
    send : function(form_name, file_input_name, cb){
      // This function is used inside of `$form.change` listener :)
      var path = location.pathname;
      if(path != "/" && path.slice(-1) == "/") path = path.slice(0, -1);
      var self = this, fd = new FormData(document.forms.namedItem(form_name));
      fd.append("key", file_input_name);
      fd.append("env", env);
      fd.append("isTest", isTest);
      fd.append("testType", testType);
      fd.append("refferer", path);
      formAjax();

      function formAjax(){
        var ajax = new XMLHttpRequest();
        ajax.open("POST", self.path, true);
        ajax.onload = function(e) {
          var res = JSON.parse(ajax.response);
          cb(res.flag, res.path);
        };
        ajax.send(fd);
      }
    }
  }


/************************************
 * DATABASE
 ************************************/
  function Mailer(host){
    this.path = host+"/mailer";
  }
  Mailer.prototype.send = function(mail_name, body_obj, cb){
    var self = this;
    var params = {};
    params.mail_name = mail_name;
    params.value = body_obj;
		$_ajax("POST", self.path, params, function(res) {
      cb(res.flag);
    });
  }

/************************************
 * DATABASE
 ************************************/
	function DataBase(table, api) {
		this.table = tableutil.norm(table);
    this.db = api+"/db";
		this.data = null;
	}

	DataBase.prototype = {
		insert: function(value, cb) {
      var self = this;
			if(value.hasOwnProperty("id")) throw new Error("cannot set id in value object");
      value.created_at = (new Date()).getTime();
      value.updated_at = (new Date()).getTime();

			var params = { table : this.table, value : value};
      $_ajax("POST", self.db+"/insert", params, function(res){
        var flag = res.flag;
        var data = res.data;
        if(data != null) data.value = JSON.parse(data.value);
        if(cb) cb(flag, data);
      });
		},
    push: function(value, cb){
      this.insert(value, cb);
    },
    delete: function(id, cb) {
			if(typeof id != "number") throw new Error("id must be number");
			var params = { id: id+"", table : this.table };
      $_ajax("POST", this.db+"/delete", params, function(res){
        if(cb) cb(res.flag, res.data);
      });
		},
    remove: function(id, cb){
      this.delete(id, cb);
    },
    update: function(id, value, cb) {
      if(typeof id == "string" || typeof id == "number" ) {
        id = parseInt(id);
        if (id == NaN) throw new Error("invalid type");
      }
			if(typeof value != "object") throw new Error("value must be object");
			if(value.hasOwnProperty("id")) throw new Error("cannot set id in value object");

      value.updated_at = (new Date()).getTime();
			var params = { id: id+"", table : this.table, value : value};

      $_ajax("POST", this.db+"/update", params, function(res){
        if(cb) cb(res.flag, res.data);
      });
		},
    set: function(id, value, cb){
      this.update(id, value, cb);
    },
    select: function(query, cb){
      var self = this;
			var params = { table : self.table, value : query};
      var selector_obj = new Selector(params, self.db);
      if(cb) selector_obj.done(cb);
      return selector_obj;
    },
    query: function(q, cb) {
      return this.select(q, cb);
		},

    count: function(query, cb){
      var self = this;
  	  var params= {};
      params.table = self.table;


      params.query = Object.keys(query).map(function (key, i) {
        return [key, query[key]];
      });
      $_ajax("GET", self.db+"/count", params, function(res){
        var count = parseInt(res.data);
        if(typeof count == "number"){
          if(cb) cb(res.flag, count);
        }
      });
    },
    first: function(cb){
			var params = { table : this.table };
      $_ajax("GET", this.db+"/first", params, function(res){
        if(cb) cb(res.flag, res.data);
      });
    },
    last: function(cb){
			var params = { table : this.table };
      $_ajax("GET", this.db+"/last", params, function(res){
        if(cb) cb(res.flag, res.data);
      });
    }
	}

/************************************
 * SEARCH
 ************************************/
	function Selector(params, db) {
		this.params = params;
		this.params.option = { };
    this.db = db;
	}
	Selector.prototype = {
		done: function(cb) {
      $_ajax("GET", this.db+"/select", this.params, function(res){
        cb(res.flag, res.data);
      });
		}
		,skip: function(skip) {
			if(!(typeof skip == "number")) {
				throw new Error("invalid skip parameter.");
			}
			this.params.option.skip = skip;
			return this;
		}
		,sort: function(_mode) {
			var mode = _mode || "desc";
			if(mode == "asc") {
				this.params.option.sort = "_priority";
			}else if(mode == "desc") {
				this.params.option.desc = "_priority";
			}else{
				throw new Error("undefined sort mode.");
			}
			return this;
		}
		,asc : function() {
			return this.sort("asc");
		}
		,desc : function() {
			return this.sort("desc");
		}
		,desort: function(attr) {
			this.params.option.desc = attr;
			return this;
		}
		,limit: function(n) {
			this.params.option.limit = n;
			return this;
		}
	}

	var tableutil = {
			norm : function(table) {
				  var a = table.split('/');
				  var b = [];
				  for(var i=0;i < a.length;i++) {
				    if(a[i] != '') {
				      b.push(a[i]);
				    }
				  }
				  return b.join('/');
			},
			parent : function(table) {
				  var a = table.split('/');
				  a.pop();
				  return a.join('/');
			},
			table_name : function(table) {
				return table.indexOf("/")
			}
	}


	function format_host(host) {
		if(host[host.length - 1] === "/") {
			return host;
		}else{
			return host + "/";
		}
	}

	function $_ajax(method, url, params, cb) {
    var path = location.pathname;
    if(path != "/" && path.slice(-1) == "/") path = path.slice(0, -1);
    params.refferer = path;
    params.env = env;
    params.isTest = isTest;
    params.testType = testType;
		var xhr = null;
		if(window.XMLHttpRequest) {
			xhr = new XMLHttpRequest();
		}else if( window.XDomainRequest ){
  		xhr = new XDomainRequest();
  	}
  	var params_str = querystring(params);
		if(method=="GET" && params_str != "") url += "?"+params_str;
  	xhr.open(method , url);
  	xhr.withCredentials = true;
  	xhr.onload = function() {
  		cb(JSON.parse(xhr.responseText));
  	}
  	xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  	xhr.send(params_str);

  	function querystring(params) {
  		var params_array = []
  		for(var key in params) {
        if(typeof params[key] == "number") params_array.push(key + "=" + encodeURIComponent(params[key]).replace('&','\&'));
        if(typeof params[key] == "string") params_array.push(key + "=" + encodeURIComponent(params[key]).replace('&','\&'));
        if(typeof params[key] == "object") params_array.push(key+"="+encodeURIComponent( JSON.stringify(params[key]) ).replace('&','\&'));
        if(typeof params[key] == "boolean") params_array.push(key+"="+params[key]);
  		}
  		return params_array.join("&");
  	}
  }

  global.HoneyBase = HoneyBase;
  return global;
}(window));
