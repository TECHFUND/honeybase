<?php include __PUBLIC__ . '/assets/header.php' ?>


  <div class="common cf">

  <!----------------------- any contents ----------------------->

  <!-- indexContents -->
  <article class="issuesContents">

    <!-- mv -->
    <section class="mvArea">
    	<div class="mv">
      	<div class="visual" style="background-image:url(../../assets/img/issues/fv_cover01.png);"></div>
      </div>
    </section>
    <!-- //mv -->

    <!-- new_issue -->
    <h2>新しい仕事を依頼(公募)する。</h2>
    <section class="new_issue w980">
    	<form onsubmit="return false;">
      <div class="form05">
        <div class="grid1">
          <h3>
            <span>1</span>
            依頼タイトル(28文字以内)
          </h3>
          <input type="input" name="title" placeholder="(例)広告運用を経験されたエキスパートにご意見を伺いたい" style="color:#adadad; width:100%;" onFocus="if(this.value==this.defaultValue){this.value='';this.style.color='#333';}" onBlur="if(this.value==''){this.value=this.defaultValue;this.style.color='#666'}" onClick="this.select();" />
        </div>
        <div class="grid2">
          <h3>
            <span>2</span>
            仕事のカテゴリーを選択
          </h3>
          <ul>
            <li><input type="radio" name="category1" checked="checked" id="radio-1" value="1" /><label for="radio-1">IT／通信</label></li>
            <li><input type="radio" name="category1" id="radio-2" value="2" /><label for="radio-2">インターネット／広告／メディア</label></li>
            <li><input type="radio" name="category1" id="radio-3" value="3" /><label for="radio-3">メーカー（機械／電気）</label></li>
            <li><input type="radio" name="category1" id="radio-4" value="4" /><label for="radio-4">メーカー（素材／化学／食品／その他）</label></li>
            <li><input type="radio" name="category1" id="radio-5" value="5" /><label for="radio-5">総合商社</label></li>
            <li><input type="radio" name="category1" id="radio-6" value="6" /><label for="radio-6">専門商社（卸）</label></li>
            <li><input type="radio" name="category1" id="radio-7" value="7" /><label for="radio-7">医療関連</label></li>
            <li><input type="radio" name="category1" id="radio-8" value="8" /><label for="radio-8">金融</label></li>
            <li><input type="radio" name="category1" id="radio-9" value="9" /><label for="radio-9">建設／プラント／不動産</label></li>
            <li><input type="radio" name="category1" id="radio-10" value="10" /><label for="radio-10">コンサルティング／リサーチ／専門事務所</label></li>
            <li><input type="radio" name="category1" id="radio-11" value="11" /><label for="radio-11">人材サービス／アウトソーシング／コールセンター</label></li>
            <li><input type="radio" name="category1" id="radio-12" value="12" /><label for="radio-12">小売</label></li>
            <li><input type="radio" name="category1" id="radio-13" value="13" /><label for="radio-13">外食</label></li>
            <li><input type="radio" name="category1" id="radio-14" value="14" /><label for="radio-14">運輸／物流</label></li>
            <li><input type="radio" name="category1" id="radio-15" value="15" /><label for="radio-15">エネルギー（電力／ガス／石油／新エネルギー）</label></li>
            <li><input type="radio" name="category1" id="radio-16" value="16" /><label for="radio-16">旅行／宿泊／レジャー </label></li>
            <li><input type="radio" name="category1" id="radio-17" value="17" /><label for="radio-17">警備／清掃</label></li>
            <li><input type="radio" name="category1" id="radio-18" value="18" /><label for="radio-18">理容／美容／エステ</label></li>
            <li><input type="radio" name="category1" id="radio-19" value="19" /><label for="radio-19">教育</label></li>
            <li><input type="radio" name="category1" id="radio-20" value="20" /><label for="radio-20">農林水産／鉱業</label></li>
            <li><input type="radio" name="category1" id="radio-21" value="21" /><label for="radio-21">公社／官公庁／学校</label></li>
            <li><input type="radio" name="category1" id="radio-22" value="22" /><label for="radio-22">冠婚葬祭</label></li>
            <li><input type="radio" name="category1" id="radio-23" value="23" /><label for="radio-23">その他</label></li>
          </ul>
        </div>
        <div class="grid2">
          <h3>
            <span>3</span>
            依頼カテゴリーを選択
          </h3>
          <ul>
            <li><input type="radio" name="category2" checked="checked" id="radio-100" value="100" /><label for="radio-100">IT／通信</label></li>
            <li><input type="radio" name="category2" id="radio-101" value="101" /><label for="radio-101">インターネット／広告／メディア</label></li>
            <li><input type="radio" name="category2" id="radio-102" value="102" /><label for="radio-102">メーカー（機械／電気）</label></li>
            <li><input type="radio" name="category2" id="radio-103" value="103" /><label for="radio-103">メーカー（素材／化学／食品／その他）</label></li>
            <li><input type="radio" name="category2" id="radio-104" value="104" /><label for="radio-104">総合商社</label></li>
            <li><input type="radio" name="category2" id="radio-105" value="105" /><label for="radio-105">専門商社（卸）</label></li>
            <li><input type="radio" name="category2" id="radio-106" value="106" /><label for="radio-106">医療関連</label></li>
            <li><input type="radio" name="category2" id="radio-107" value="107" /><label for="radio-107">金融</label></li>
            <li><input type="radio" name="category2" id="radio-108" value="108" /><label for="radio-108">建設／プラント／不動産</label></li>
            <li><input type="radio" name="category2" id="radio-109" value="109" /><label for="radio-109">コンサルティング／リサーチ／専門事務所</label></li>
            <li><input type="radio" name="category2" id="radio-110" value="110" /><label for="radio-110">人材サービス／アウトソーシング／コールセンター</label></li>
            <li><input type="radio" name="category2" id="radio-111" value="111" /><label for="radio-111">小売</label></li>
            <li><input type="radio" name="category2" id="radio-112" value="112" /><label for="radio-112">外食</label></li>
            <li><input type="radio" name="category2" id="radio-113" value="113" /><label for="radio-113">運輸／物流</label></li>
            <li><input type="radio" name="category2" id="radio-114" value="114" /><label for="radio-114">エネルギー（電力／ガス／石油／新エネルギー）</label></li>
            <li><input type="radio" name="category2" id="radio-115" value="115" /><label for="radio-115">旅行／宿泊／レジャー </label></li>
            <li><input type="radio" name="category2" id="radio-116" value="116" /><label for="radio-116">警備／清掃</label></li>
            <li><input type="radio" name="category2" id="radio-117" value="117" /><label for="radio-117">理容／美容／エステ</label></li>
            <li><input type="radio" name="category2" id="radio-118" value="118" /><label for="radio-118">教育</label></li>
            <li><input type="radio" name="category2" id="radio-119" value="119" /><label for="radio-119">農林水産／鉱業</label></li>
            <li><input type="radio" name="category2" id="radio-120" value="120" /><label for="radio-120">公社／官公庁／学校</label></li>
            <li><input type="radio" name="category2" id="radio-121" value="121" /><label for="radio-121">冠婚葬祭</label></li>
            <li><input type="radio" name="category2" id="radio-122" value="122" /><label for="radio-122">その他</label></li>
          </ul>
        </div>
        <div class="grid1">
          <h3>
            <span>4</span>
            依頼内容を記入(フリーコメント)
          </h3>
          <textarea cols="50" rows="5" name="body" placeholder="【現状】
自社メディアの広告運用を代理店にまかせっきりでしたが、今後は自社で運用をしっかりと
ディレクションしていきたいと考えています。
【今後】
運用チームを組成し、強化していきたい
【依頼したいこと】
弊社の広告運用における戦略、戦術の策定、及び運用体制の構築(マニュアルの策定や担当者の教育)をご依頼したい"></textarea>
        </div>
        <div id="account_grid" style="display:none;" class="grid1">
          <h3>
            <span>5</span>
            アカウント(未登録の方)
          </h3>
          <ul>
            <li><input type="radio" name="login" id="oauth_radio" checked="checked" value="oauth" /><label for="oauth_radio">Facebookで登録</label></li>
            <li><input type="radio" name="login" id="email_radio" value="email" /><label for="email_radio">Emailで登録</label></li>
          </ul>
          <div id='email_form' style="display:none;">
            <lebel>Email: <input cols="50" rows="5" id="when_issue_email" name="email" type="input" placeholder="info@..." /></lebel>
            <lebel>希望のPassword: <input cols="50" rows="5" id="when_issue_password" name="password" type="password" placeholder="********" /></lebel>
          </div>
        </div>
        <br style="clear:both">
        <div class="cv_btn">
        	<input type="submit" placeholder="ここの内容で依頼する"  class="btn02_4" />
        </div>
      </div>
      </form>
      <br style="clear:both">
    </section>

  </article>
  <!-- //indexContents -->

  <!----------------------- //any contents ----------------------->

  </div>


  <script>
    (function(global){
      function NewIssue(){}
      NewIssue.validate = function(params){
        var titleFilled = (typeof params.title !== 'undefined' && params.title !== "");
        var category1Filled = (typeof params.category1 !== 'undefined' && params.category1 !== "");
        var category2Filled = (typeof params.category2 !== 'undefined' && params.category2 !== "");
        var bodyFilled = (typeof params.body !== 'undefined' && params.body !== "");
        var res = titleFilled && category1Filled && category2Filled && bodyFilled;

        if(res) {
          return true;
        } else {
          var msg = "blank value";
          Notice.error(msg);
          return false;
        }
      }

      NewIssue.tutorial = function(current_user){
        Notice.success(current_user.full_name+"さん、SkillSharedにようこそ！");
        setTimeout(function(){
          Notice.success("まずはスクロールして、タイトルを埋めてみましょう！");
          setTimeout(function(){
            Notice.success("そしてカテゴリと、聞きたい内容を埋めていきましょう！");
            setTimeout(function(){
              Notice.success("オススメの専門家を見繕っていますので、メール通知をお待ちくださいね！");
            }, 5000);
          }, 5000);
        }, 5000);
      }

      NewIssue.create = function(params, current_user){
        if ( NewIssue.validate(params) ) {
          params.user_id = current_user.id;
          IssueDB.insert(params, function(flag, issue){
            if(flag) AI.redirect("/client/issues/"+issue.id+"?success_flag=true");
            else Notice.error("connection error");
          });
        } else {
          return false;
        }
      }


      global.NewIssue = NewIssue;
      return global;
    }(window));

    $(function(){
      honeybase.current_user(function(flag, current_user){
        if(flag){
          if(AI.params().tutorial) NewIssue.tutorial(current_user);

          $(".btn02_4").click(function(e){
            var params = {
              title: $("input[name=title]").val(),
              category1: $("input[name=category1]:checked").val(),
              category2: $("input[name=category2]:checked").val(),
              body: $("textarea[name=body]").val()
            }
            NewIssue.create(params, current_user);
          });
        } else {
          // 非ログインの場合はアカウント登録を同時に行う必要がある。
          $("#account_grid").show();
          $("input[name='login']:radio").change(function(){
            if($(this).val() == "email") $("#email_form").show("slow");
            else $("#email_form").hide("slow");
          });

          $(".btn02_4").click(function(e){
            var params = {
              title: $("input[name=title]").val(),
              category1: $("input[name=category1]:checked").val(),
              category2: $("input[name=category2]:checked").val(),
              body: $("textarea[name=body]").val()
            }

            // issueの値が正しくないとユーザーを作らせない(ユーザーを作ってからvalidateでは遅い)
            if ( NewIssue.validate(params) ) {
              // 種類に応じてログイン方法を変える
              switch($("input[name=login]:checked").val()){
                case "oauth":
                  honeybase.auth("facebook", {type:"client"}, function(flag, user){
                    NewIssue.create(params, user);
                  });
                  break;
                case "email":
                  var email = $("#when_issue_email").val();
                  var password = $("#when_issue_password").val();
                  if( Modal.emailAuthChecker(email, password) ){
                    honeybase.signup(email, password, {type:"client"}, function(flag, user){
                      // email認証してからissuesに行かねばならぬ
                      NewIssue.create(params, user);
                    });
                  } else {
                    Notice.error("emailかpasswordが不正です");
                  }
                  break;
                default:
                  Notice.error("エラー");
                  break;
              }
            } else {
              Notice.error("エラー");
            }
          });
        }
      });
    });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
