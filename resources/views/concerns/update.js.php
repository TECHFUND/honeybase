<script>
  (function(global){
		var $team_name = $(".formArea").find("h2"), $form = $(".company-form");
		var $user_photo = $("#user-img"), $company_photo = $("#company-img"), $camera_icon = $("#team_camera");
    var $team_list = $("select[name=options]").children();
    var $user_uploader = $('#hidden_user_uploader'), $company_uploader = $('#hidden_company_uploader');
    var $users_form = $('form[name=users]'), $teams_form = $('form[name=teams]');

    function PROFILE_UPDATE(){}

    /*
    * USABILITY FUNCTIONSs
    */
    PROFILE_UPDATE.defaultValueSetter = function(){
  		$('#company_tab').click(function(e){
  			var i = $(this).index(this);
  			var p = $("#company_head").eq(i).offset().top;
        $('html,body').animate({ scrollTop: p-150 }, 'slow');
  		});

  		$('input').each(function(){
  	    $(this).val($(this).attr('defaultvalue'));
  			//$(this).focus(onFocusBlank);
  			//$(this).blur(onBlurAdd);
  		});
  		$('textarea').each(function(){
  	    $(this).val($(this).attr('defaultvalue'));
  			$(this).focus(onFocusBlank);
  			$(this).blur(onBlurAdd);
  		});

  		function onFocusBlank(e){
  	    if (!$(this).hasClass("pure")) {return 0;}
  	    $(this).toggleClass('pure');
  	    $(this).val("");
  	    return 1;
  		}
  		function onBlurAdd(e){
  	    if ($(this).val() != "") {return 0;}
  	    $(this).toggleClass("pure");
  	    $(this).val($(this).attr('defaultvalue'));
  	    return 1;
  		}
    }

    /*
    * BASE FUNCTIONS
    */
    PROFILE_UPDATE.submitListener = function(current_user){
      $("#register").click(function(){
				var team_id = AI.escape($(".select01").val());
        var techfund_id = AI.escape($("input[name=techfund_id]").val());
				if(team_id == -1){
          // チームを新しく作る
          var team_name = AI.escape($("input[name=name]").val());
          TeamDB.count({name: team_name}, function(flag, i){
            if(i == 0){
    	        var team_params = {
    						name: team_name,
    						service_name: AI.escape($("input[name=service_name]").val()),
    						team_cover: PROFILE_UPDATE.currentTeamCover(),
    						link: AI.escape($("input[name=link]").val()),
    						service_link: AI.escape($("input[name=service_link]").val()),
    						owner_id: current_user.id,
    						description: AI.escape($("textarea[name=description]").val())
    	        };
    					TeamDB.insert(team_params, function(flag, team){
    						if(flag) user_update(team.id, techfund_id);
    						else Notice.error("Something wrong for create team");
    					});
            } else {
              Notice.error("Team name duplicated");
              return false;
            }
          });
				} else {
					user_update(team_id, techfund_id);
				}
      });

			function user_update(team_id, techfund_id){
        var email = AI.escape($("input[name=email]").val());
        var r = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        if(!r.test(AI.decode(email))) {
          Notice.error("Invalid email");
          return false;
        }
        var user_params = {
          techfund_id: techfund_id,
          email: email,
          last_name: AI.escape($("input[name=last_name]").val()),
          first_name: AI.escape($("input[name=first_name]").val()),
          description: AI.escape($("textarea[name=profile]").val()),
					team_id: team_id,
          picture: PROFILE_UPDATE.currentProfilePicture()
        };
        honeybase.current_user(function(flag, current_user){
          var current_id = parseInt(current_user.id, 10);
          UserDB.update(current_id, user_params, function(flag, user){
            if(flag) AI.redirect("/my/feed?success_flag=true");
            else Notice.error("User creation failure");
          });
        });
			}
    }

    PROFILE_UPDATE.currentProfilePicture = function(){
      var str = AI.escape($user_photo.css("background-image"));
      var url = str.slice(4, str.length-1);
      return url;
    }

    PROFILE_UPDATE.currentTeamCover = function(){
      var str = AI.escape($company_photo.css("background-image"));
      var url = str.slice(4, str.length-1);
      return url;
    }

    global.PROFILE_UPDATE = PROFILE_UPDATE;
    return global;
  }(window));
</script>
