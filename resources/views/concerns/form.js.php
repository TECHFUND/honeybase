<script>
  (function(global){

		var $team_name = $(".formArea").find("h2"), $form = $(".company-form");
		var $user_photo = $("#user-img"), $company_photo = $("#company-img"), $camera_icon = $("#team_camera");
    var $team_list = $("select[name=options]").children();
    var $user_uploader = $('#hidden_user_uploader'), $company_uploader = $('#hidden_company_uploader');
    var $users_form = $('form[name=users]'), $teams_form = $('form[name=teams]');

    var $input_user_techfundid = $('input[name=techfund_id]');
    var $input_user_email = $('input[name=email]');
    var $input_user_lastname = $('input[name=last_name]');
    var $input_user_firstname = $('input[name=first_name]');
    var $input_user_profile = $('textarea[name=profile]');
    var $input_user_pic = $('input[name=users_pic]');
    var $input_team_name = $('input[name=name]');
    var $input_team_link = $('input[name=link]');
    var $input_team_description = $('input[name=description]');
    var $input_team_pic = $('input[name=teams_pic]');


    var responses = [
      'Good one :)',
      'Cool ;)',
      'Elegant <3',
      'Marverous XD',
      'Wow XD',
      'Nice, go next dude :)'
    ]

    function Form () {}

    /*
    * USABILITY FUNCTIONS
    */
    Form.validator = function(opt){
      var isEdit = false;
      if(opt && opt.edit) isEdit = opt.edit;

      techfund_id = $input_user_techfundid.val();
      email = $input_user_email.val();

      $input_user_techfundid.keypress(function(e){
        Form.techfundIdProcess($(this), e, isEdit);
      });
      $input_user_techfundid.blur(function(e){
        // 変更があったときのみ
        if (techfund_id != $input_user_techfundid.val()) {
          Form.techfundIdProcess($(this), e, isEdit);
        }
      });
      $input_user_email.keypress(function(e){
        Form.emailProcess($(this), e, isEdit);
      });
      $input_user_email.blur(function(e){
        // 変更があったときのみ
        if (email != $input_user_email.val()) {
          Form.emailProcess($(this), e, isEdit);
        }
      });
      $input_team_link.keypress(function(e){
        Form.padLink($(this), e);
      });
      $input_team_link.blur(function(e){
        var $self = $(this);
        if(AI.escape($self.val()).length > 0){
          Form.padLink($self, e);
        }
      });
      $input_team_description.keypress(function(e){
        if(e.which == 13) $('#register').trigger('click');
      });
    }

    Form.padLink = function($self, e){
      var w = AI.escape($self.val());
      var r = /^https?:\/\//;
      if(e.which == 13 || e.type == "blur"){
        if(!r.test(w)) $self.val("http://"+w);
        //Form.message(res, 'green', $self.position().top);
        $input_team_description.focus();
      }
    }

    Form.onSomethingEntered = function(e, cb){
      setTimeout(function(){
        var it = AI.escape(e.target.value);
        if(e.which == 13 || e.type == "blur"){
          if(it.length > 0) cb(it);
        }
      },0);
    }

    Form.techfundIdProcess = function($self, e, isEdit){
      Form.onSomethingEntered(e, function(it){
        UserDB.count({techfund_id: it}, function(flag, i){
          if(flag){
            if(i > 0){
              if(isEdit) Form.message('DUPLICATED or NOT CHANGED', 'red', $self.position().top);
              else Form.message('DUPLICATED', 'red', $self.position().top);
            } else {
              Form.message('UNIQUE', 'green', $self.position().top);
              $input_user_email.focus();
            }
          } else {
            Form.message('ERROR OCCURED', 'blue', $self.position().top);
          }
        });
      });
    }

    Form.emailProcess = function($self, e, isEdit){
      Form.onSomethingEntered(e, function(it){
        UserDB.count({email: it}, function(flag, i){
          if(flag){
            if(i > 0){
              if(isEdit) Form.message('DUPLICATED or NOT CHANGED', 'red', $self.position().top);
              else Form.message('DUPLICATED', 'red', $self.position().top);
            } else {
              var r = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
              if(r.test(AI.decode(it))){
                Form.message('UNIQUE', 'green', $self.position().top);
                $input_user_lastname.focus();
              } else {
                Form.message('INVALID', 'red', $self.position().top);
              }
            }
          } else {
            Form.message('ERROR OCCURED', 'green', $self.position().top);
          }
        });
      });
    }


    Form.setMessageArea = function(){
      $('body').prepend("<div id='error' style='padding: 0px 10px;color:#FFF;opacity:0.8;position:absolute;width:200px;height:30px;border-radius:9px;'></div>")
    }

    Form.message = function(text,color,top){
      var $err = $('#error');
      var w = $(window).width();
      $err.css('background-color', color);
      $err.css('top', top+"px");
      $err.css('left', (w/3)+"px");
      $err.text(text);
      $err.fadeIn("slow");
      if(text.length > 16) $err.css('height', "45px");
      setTimeout(function(){
        $err.fadeOut("slow");
      }, 3000);
    }

    Form.focusFirst = function(){
      $('input:visible').first().focus();
    }






    /*
    * USER FUNCTIONS
    */
    Form.userFormListener = function(){
			$user_photo.click(function(e){
				Form.uploadProfilePicture();
			});
    }

		Form.uploadProfilePicture = function(){
			$user_uploader.trigger("click");
			// $users_form.off("change").change(function(e){
			$user_uploader.off("change").change(function(e){
        Uploader.send("users", "users_pic", function(flag, path){
          if(flag) $user_photo.css("background-image","url(/assets/img/generated/"+path+")");
        });
			});
		}

    /*
    * COMPANY FUNCTIONS
    */
    Form.companyListener = function(){
      if($team_list.length > 1){
  			$camera_icon.hide();
        $(".select01").off("change").change(function(e){
  				$camera_icon.hide();
  				var team_id = e.target.value;
          if(team_id == '-1') Form.newTeam();
          else Form.existingTeam(team_id);
        });
      } else Form.newTeam();
    }

    Form.newTeam = function(){
			$camera_icon.show();
      $form.show();
			Form.changeIcon("/assets/img/common/company02.gif");
			$team_name.text("NEW TEAM");
			$company_photo.off('click').click(function(e){
				Form.uploadTeamCover();
			});
    }

    Form.existingTeam = function(team_id){
			Form.changeIconFromDB(team_id);
      $form.hide();
			$company_photo.off('click');
    }

		Form.uploadTeamCover = function(){
			$company_uploader.trigger("click");
			$company_uploader.off("change").change(function(e){
        Uploader.send("teams", "teams_pic", function(flag, path){
          if(flag) $company_photo.css("background-image","url(/assets/img/generated/"+path+")");
        });
			});
		}

		Form.changeIcon = function(url){
      if(url.split("/").length < 2) url = "/assets/img/generated/" + url;
      $company_photo.css("background-image", "url("+url+")");
    }

		Form.changeIconFromDB = function(team_id){
      // teamを消している場合、描画されるidとDBのidが食い違うためにselectが-1されたデータを取得してしまう
			TeamDB.select({id:team_id}).done(function(flag, teams){
				if(flag)  Form.changeIcon(teams[0].team_cover);
				else Form.changeIcon("https://scontent.xx.fbcdn.net/hphotos-xfp1/v/t1.0-9/10616151_776504119039343_7236294971851520386_n.jpg?oh=976983729d54bee72fc3bded0ef7a600&oe=55ED6DDC");
				$team_name.text(teams[0].name);
			});
		}
    global.Form = Form;
    return global;
  }(window));
</script>
