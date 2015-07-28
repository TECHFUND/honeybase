<?php include __PUBLIC__ . 'assets/header.php' ?>
<?php
  function my_team_cover($teams, $current_user){
    $url = __PUBLIC__.__TECHFUNDICON__;
    foreach($teams as $team){
      if($team['id'] == $current_user['team_id']){
        $url = $team['team_cover'];
      }
    }
    return $url;
  }
?>
<h2>PROFILE EDIT</h2>

<!-- common -->
<div class="common w80 clearfix">
  <div class="tab">
    <ul>
			<li id="yourself_tab" class="transition current">Yourself</li>
			<a href="#" id="company_tab"><li class="transition">Company</li></a>
      <br style="clear:both">
    </ul>
  </div>



  <div class="profileContents">

    <!-- form -->
    <article class="formArea">

    	<div class="photo">
      	<p class="img01_1" id="user-img" style="background-image:url(<?php echo $current_user['picture']; ?>); cursor: pointer;">
        	<span><i class="fa fa-camera"></i></span>
        </p>
      </div>


      <form name='users' enctype="multipart/form-data">
        <div class="form03">
          <ul>
            <li>
              <h3>MAIL ADRESS</h3>
              <dl><input type="input" name="email" placeholer="info@growther.jp" style="color:#adadad; width:100%;" defaultvalue="<?php echo $current_user['email']; ?>" />
              </dl>
            </li>
            <li>
              <h3>PROFILE</h3>
              <dl><textarea cols="50" rows="5" name="profile" placeholder="please input" defaultvalue="<?php echo $current_user['description']; ?>"></textarea></dl>
            </li>
            <li>
              <h3>SKILLS</h3>
              <dl><input name="skill"/></br>
                <ul class="skilltag"><?php foreach($skills as $skill){ echo
                  '<li id="skill'.$skill['id'].'">'.$skill['name'].'</li>'; } ?>
                </ul>
                <ul class="search_result">
                </ul>
              </dl>
            </li>
          </ul>
					<input type="file" name="users_pic" style="display:none;" id="hidden_user_uploader" />
        </div>
      </form>

    </article>
  </div>

  <style>
    .skilltag {
      display: flex;
      flex-direction: row;
      align-items: flex-start;
    }

    .search_result{
      display: none;
      width: 200px;
      height: 300px;
      background-color: gray;
    }
  </style>


<?php include __DIR__.'/../concerns/update.js.php'; ?>
<?php include __DIR__.'/../concerns/form.js.php'; ?>
<script>
  (function(global){
    var $result = $(".search_result");


    function EDIT_PROFILE(){}
    EDIT_PROFILE.renderSkill = function(skill, current_user){
      $(".skilltag").append('<li id="skill'+AI.escape(skill.id)+'">'+AI.decode(AI.escape(skill.name))+'</li>');

      $('#skill'+AI.escape(skill.id)).click(function(e){
        EDIT_PROFILE.removeExpertSkill($(this), current_user);
      });

    }

    EDIT_PROFILE.searchSkill = function($self, e, current_user){
      var self = this;
      if($self.val().length > 0){
        honeybase.ajax("GET", "/expert/skills/search", {value: {name: $self.val()}}, function(data){
          var flag = data.flag, skills = data.data;
          if(flag){
            $result.show();
            skills.map(function(skill){
              $result.append('<li id="result_skill'+skill.id+'">'+skill.name+'</li>');
              $searched_skill = $("#result_skill"+skill.id);

              $searched_skill.click(function(e){
                ExpertSkillDB.insert({expert_id: current_user.id, skill_id: $(this).attr("id").split("result_skill")[1], grade:3}, function(flag, res){
                  if(flag) EDIT_PROFILE.renderSkill(skill, current_user);
                  else Notice.error("Skill insertion error");
                });
              });

            })
          } else {
            $result.empty();
          }
        });
      }
    }

    EDIT_PROFILE.createSkill = function($self, e){
      SkillDB.count({name: $self.val()}, function(flag, i){
        if(i > 0){
          Notice.error("That name skill already exists, choose from list");
        } else {
          SkillDB.insert({name: $self.val()}, function(flag, skill){
            if(flag) {
              EDIT_PROFILE.renderSkill(skill);
              Notice.success("New skill created");
            } else Notice.error("Failed to create new skill");
          });
        }
      });
    }

    EDIT_PROFILE.searchOrCreateSkill = function(current_user){
      $("input[name=skill]").keydown(function(e){
        $result.empty();
        var $self = $(this);
        setTimeout(function(){
          if(e.which != 13) EDIT_PROFILE.searchSkill($self, e, current_user);
          else EDIT_PROFILE.createSkill($self, e);
        }, 0);
      });
    }

    EDIT_PROFILE.onLeaveInput = function(){
      $("input[name=skill]").blur(function(e){
        setTimeout(function(){
          $result.empty();
          $result.hide();
        }, 300);
      });
    }

    EDIT_PROFILE.onClickCross = function(current_user){
      $("[id^=skill]").click(function(e){
        EDIT_PROFILE.removeExpertSkill($(this), current_user);
      });
    }

    EDIT_PROFILE.removeExpertSkill = function($self, current_user){
      var skill_id = $self.attr('id').split("skill")[1];
      ExpertSkillDB.select({skill_id:skill_id, expert_id:current_user.id}).done(function(flag, expert_skills){
        expert_skills.map(function(expert_skill){
          ExpertSkillDB.delete(parseInt(expert_skill.id), function(flag){
            if(flag) {
              $("#skill"+skill_id).remove();
              Notice.success("Success to delete a data");
            } else Notice.error("Success to delete a data");
          });
        });
      });
    }

    global.EDIT_PROFILE = EDIT_PROFILE;
    return global;
  }(window));


  $(function(){
		PROFILE_UPDATE.defaultValueSetter();
		Form.setMessageArea();
    Form.validator({edit:true});
    Form.userFormListener();
    Form.companyListener();
		Form.focusFirst();
    PROFILE_UPDATE.submitListener();

    honeybase.current_user(function(flag, current_user){
      EDIT_PROFILE.searchOrCreateSkill(current_user);
      EDIT_PROFILE.onClickCross(current_user);
    });

    EDIT_PROFILE.onLeaveInput();
  });
</script>

<?php include __PUBLIC__ . 'assets/footer.php'; ?>
