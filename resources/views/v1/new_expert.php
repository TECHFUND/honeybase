<?php include __PUBLIC__ . '/assets/header.php' ?>


  <div class="common cf">

  <!----------------------- any contents ----------------------->

  <!-- issuesContents -->
  <article class="issuesContents">
<style>
.bbb {
      border:#c1c1c1 solid 1px !important;
      border-radius: 5px !important;
    }
</style>
    <!-- regist_issue -->
    <h2>新規ユーザー登録</h2>
    <section class="regist_issue w980">
      <form id="new_expert">
      <div class="formarea form06">
      	<h3>メールアドレス以外の情報を入力し、登録を完了してください。</h3>
        <ul>
          <li>
            <dl><input class="bbb" type="input" name="email" placeholder="info@example.com" style="color:#666; width:100%;" onFocus="if(this.value==this.defaultValue){this.value='';this.style.color='#8e8e8e';}" onBlur="if(this.value==''){this.value=this.defaultValue;this.style.color='#666'}" onClick="this.select();" />
            </dl>
          </li>
          <li>
            <dl><img id="picture" src="<?php echo __GUESTICON__; ?>" />
            <input class="bbb" type="file" name="picture" style="display:none;" />
            </dl>
          </li>
          <li>
            <dl><input class="bbb" type="input" name="company" placeholder="会社名" style="color:#666; width:100%;" onFocus="if(this.value==this.defaultValue){this.value='';this.style.color='#8e8e8e';}" onBlur="if(this.value==''){this.value=this.defaultValue;this.style.color='#666'}" onClick="this.select();" />
            </dl>
          </li>
          <li>
            <dl><input class="bbb" type="input" name="position" placeholder="役職" style="color:#666; width:100%;" onFocus="if(this.value==this.defaultValue){this.value='';this.style.color='#8e8e8e';}" onBlur="if(this.value==''){this.value=this.defaultValue;this.style.color='#666'}" onClick="this.select();" />
            </dl>
          </li>
          <li>
            <dl><input class="bbb" type="input" name="username" placeholder="名前" style="color:#666; width:100%;" onFocus="if(this.value==this.defaultValue){this.value='';this.style.color='#8e8e8e';}" onBlur="if(this.value==''){this.value=this.defaultValue;this.style.color='#666'}" onClick="this.select();" />
            </dl>
          </li>
          <li>
            <dl><input class="bbb" type="input" name="career" placeholder="経歴" style="color:#666; width:100%;" onFocus="if(this.value==this.defaultValue){this.value='';this.style.color='#8e8e8e';}" onBlur="if(this.value==''){this.value=this.defaultValue;this.style.color='#666'}" onClick="this.select();" />
            </dl>
          </li>
          <li>
            <dl><textarea cols="50" rows="5" name="experience" style="border-radius: 5px !important;" placeholder="過去の経験"></textarea></dl>
          </li>
          <li>
            <dl><textarea cols="50" rows="5" name="pr" style="border-radius: 5px !important;" placeholder="自己PRをしてください。"></textarea></dl>
          </li>
        </ul>
      </div>
      </form>
      <button class="btn02_4">この内容で登録する</button>
      <br style="clear:both">
    </section>
    <!-- //regist_issue -->

  </article>
  <!-- //issuesContents -->

  <!----------------------- //any contents ----------------------->

  </div>


  <script>
    (function(global){
      function NewExpert(){}

      NewExpert.upload = function(cb){
        var self = this;
        Uploader.send("new_expert", "picture", function(flag, path){
          self.uploaded_path = path;
          if(flag) {
            cb(path);
          } else Notice.error("upload failed");
        });
      }
      NewExpert.validate = function(params1, params2){
        var a = params2.company != "";
        var b = params2.position != "";
        var c = params1.full_name != "";
        var d = params1.description != "";
        var e = params2.verify;
        var res = a && b && c && d && e;
        if(res){
        } else {
          var msg = "blank value";
          Notice.error(msg);
          throw msg;
        }
      }

      NewExpert.submitListener = function(current_user, expert){
        $(".btn02_4").click(function(e){
          var params1 = {
            email: $("input[name=email]").val(),
            full_name: $("input[name=username]").val(),
            description: $("textarea[name=pr]").val(),
            picture: $("#picture").attr("src")
          }
          var params2 = {
            company: $("input[name=company]").val(),
            position: $("input[name=position]").val(),
            verify: true,
            user_id: current_user.id
          }
          NewExpert.validate(params1, params2);

          UserDB.update(current_user.id, params1, function(flag, user){
            if(flag) {
              ExpertDB.update(expert.id, params2, function(flag, expert){
                if(flag) AI.redirect("/expert/waiting");
                else Notice.error("connection error");
              });
            } else Notice.error("connection error");
          });
        });
      }

      global.NewExpert = NewExpert;
      return global;
    }(window));

    $(function(){
      honeybase.current_user(function(flag, current_user){
        if(flag) {
          ExpertDB.select({user_id: current_user.id}, function(flag, experts){
            if(flag) {
              NewExpert.submitListener(current_user, experts[0]);
              $("input[name=picture]").off("change").change(function(e){
                NewExpert.upload(function(path){
                  $("#picture").attr("src","/assets/img/generated/"+path);
                });
              });
              $("#picture").click(function(e){
                $("input[name=picture]").trigger("click");
              });
            } else {
              ExpertDB.insert({company:"", position:"", user_id: current_user.id}, function(flag, expert){
                if(flag) NewExpert.submitListener(current_user, expert);
                else Notice.error("something wrong");
              });
            }
          });
        } else {

        }
      });
    });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
