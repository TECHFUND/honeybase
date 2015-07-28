<?php include __PUBLIC__ . '/assets/header.php' ?>

  <h2 style="margin-top:200px;">Admin Login</h2>
  <section class="l_r">
    <div class="formarea form06">

      <h3>ソーシャルアカウントで登録する</h3>
      <ul>
        <li>
        	<button id="admin_facebook_login" class="btn02_6">Facebookで登録する</button>
        </li>
      </ul>

      <h3>メールアドレスで登録する</h3>
      <ul>
        <li>
          <dl><input type="input" name="email" placeholder="メールアドレスを入力してください。" style="color:#adadad; width:100%;" onFocus="if(this.value==this.defaultValue){this.value='';this.style.color='#8e8e8e';}" onBlur="if(this.value==''){this.value=this.defaultValue;this.style.color='#666'}" onClick="this.select();" />
          </dl>
        </li>
        <li>
          <dl><input type="password" name="password" placeholder="パスワードを入力してください。" style="color:#adadad; width:100%;" onFocus="if(this.value==this.defaultValue){this.value='';this.style.color='#8e8e8e';}" onBlur="if(this.value==''){this.value=this.defaultValue;this.style.color='#666'}" onClick="this.select();" />
          </dl>
        </li>
        <li>
        	<button id="admin_email_signup" class="btn02_4" style="width:48%;">登録</button>
        	<button id="admin_email_signin" class="btn02_4" style="width:48%;">ログイン</button>
        </li>
      </ul>
    </div>
  </section>


  <script>
    (function(global){
      function AdminLogin(){}
      AdminLogin.auth = function(){
        honeybase.auth("facebook", {type: 'admin'}, function(flag, user){
          if(flag) {
            Notice.success(user.full_name+"さんとしてログインに成功しました。専門家として案件に推薦しますので、依頼メールが来るのをお待ちください。");
            setTimeout(function(){
              AI.redirect("/admin");
            }, 800);
          } else {
            Notice.error(user.full_name+"さんは"+user.type+"としてアカウントを作成しています。adminとしてログインできませんでした。");
          }
        });
      }

      AdminLogin.emailAuthChecker = function(email, password){
        var res = false;
        var pattern = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/
        if(email.length * password.length > 0 && pattern.test(email) && password.length > 7) {
          res = true;
        } else if (email.length * password.length < 1) {
          Notice.error("emailかpasswordが空白です");
        } else if (!pattern.test(email)) {
          Notice.error("emailのフォーマットが不正です");
        } else if (password.length < 8) {
          Notice.error("passwordは8文字以上でお願いします");
        } else {
          Notice.error("予期せぬエラーです。お手数ですが、管理者に連絡してください。");
        }
        return res;
      }

      AdminLogin.signupListener = function(){
  			$("#admin_email_signup").on("click", function(e){
          var email = $("input[name=email]").val();
          var password = $("input[name=password]").val();
          if(AdminLogin.emailAuthChecker(email, password)){
    				honeybase.signup(email, password, {type: 'admin'}, function(flag, user){
              ExpertDB.insert({user_id:user.id}, function(flag, expert){
                if(flag) Notice.success("メールを送信しました。本登録を完了してください。"); // expertのときとclientのときで送るメールは別
                else Notice.error("通信エラー");
              });
    				});
          } else {
            Notice.error("正しいemail/passwordを入力してください");
          }
  			});
      }

      AdminLogin.signinListener = function(){
  			$("#admin_email_signin").on("click", function(e){
          var email = $("input[name=email]").val();
          var password = $("input[name=password]").val();
          if(AdminLogin.emailAuthChecker(email, password)){
    				honeybase.signin(email, password, function(flag, user){
              if(flag) {
                AdminLogin.redirectAfterLogin(user);
              } else Notice.error("通信エラー");
    				});
          } else {
            Notice.error("正しいemail/passwordを入力してください");
          }
  			});
      }

      AdminLogin.redirectAfterLogin = function(user){
        switch(user.type){
          case "client":
            AI.redirect("/client/issues?success_flag=true");
            break;
          case "expert":
            AI.redirect("/expert/issues?success_flag=true");
            break;
          case "admin":
            AI.redirect("/admin?success_flag=true");
            break;
          default:
            Notice.error("不正なtypeです");
            break;
        }
      }

      global.AdminLogin = AdminLogin;
      return global;
    }(window));

    $(function(){
      honeybase.current_user(function(flag, current_user){
  			$("#admin_facebook_login").on("click", function(e){
          AdminLogin.auth();
  			});
        AdminLogin.signupListener();
        AdminLogin.signinListener();
      });
    });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
