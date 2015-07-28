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
          </ul>
					<input type="file" name="users_pic" style="display:none;" id="hidden_user_uploader" />
        </div>
      </form>

    </article>
  </div>

<?php include __DIR__.'/../concerns/update.js.php'; ?>
<?php include __DIR__.'/../concerns/form.js.php'; ?>
<script>
  $(function(){
		PROFILE_UPDATE.defaultValueSetter();
		Form.setMessageArea();
    Form.validator({edit:true});
    Form.userFormListener();
    Form.companyListener();
		Form.focusFirst();
    PROFILE_UPDATE.submitListener();
  });
</script>

<?php include __PUBLIC__ . 'assets/footer.php'; ?>
