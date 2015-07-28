<?php include __PUBLIC__ . '/assets/header.php' ?>



  <!-- common -->
  <div class="common cf">

  <!----------------------- any contents ----------------------->

  <!-- chatContents -->
  <article class="chatContents">

    <!-- chat_detail -->
    <h2><?php echo $issue['title']; ?> : to_offline</h2>
    <section class="chat_detail w980">
      <div class="messagearea">
        <ul><?php if( count($messages) == 0 ){ echo '<li class="me"></li>'; } else { foreach($messages as $message) { echo
          '<li class="me" data-n="'.$message['id'].'">'.
            '<dl>'.
              '<dt>'.
                '<div class="thmb" style="background-image:url('.$message['picture'].');"></div>'.
                '<a href="#">'.$message['full_name'].'</a>さん'.
              '</dt>'.
              '<dd>'.
                '<div class="message">'.
                  //'<!--p class="title"><i class="fa fa-comments"></i><a href="#">マーケティングプロジェクトA(ここにタイトル)</a></p>'.
                  //'<p class="title"><i class="fa fa-users"></i><a href="#">倉井</a> / <a href="#">大芝</a> / <a href="#">小沢</a></p-->'.
                  '<p class="date">投稿日時 ：'.$message['created_at'].'</p>'.
                  '<p class="txt">'.$message['body'].
                  '</p>'.
                '</div>'.
              '</dd>'.
              '<br style="clear:both">'.
            '</dl>'.
          '</li>' ; } } ?>
        </ul>
      </div>
      <div class="sidemenu">
        <h3>アサインメンバーです</h3>

        <div class="thmb"><?php $i=0; foreach($executors as $executor){ $i++; echo
          '<p class="imgbox" style="background-image:url('.$executor['picture'].');" data-n="'.$executor['id'].'"></p>'; if($i%3 == 0) { echo '<br style="clear:both">'; }; } ?>
          <br style="clear:both">
          <p class="txt">他のSNSを通じての中抜き行為は規約で禁止されております。</p>
          <button id="delete" class="btn02_4">除外</button>
        </div>
        <h3 style="margin-top:20px;">サポート</h3>
          <p class="txt">何かわからないことがあれば、Skill sharedサポートチームがサポート致します。プロジェクトID(PJ_数字5桁)を件名に書いて、<a href="#">info@skillshared.com</a>までご連絡ください。</p>
      </div>
      <br style="clear:both">
      <div class="messagebox form03">
        <ul>
          <li>
            <dl>
              <dt>
                <textarea cols="50" rows="5" name="body" placeholder="please input"></textarea>
                <p class="txt">※FacebookなどのSNSでエキスパートと直接連絡をする行為は禁止されております。</p>
              </dt>
              <dd><button id="submit" class="btn02_7">send</button></dd>
            </dl>
          </li>
        </ul>
      </div>

    </section>
    <!-- //chat_detail -->

  </article>
  <!-- //chatContents -->

  <!----------------------- //any contents ----------------------->

  </div>
  <!-- //common -->

  <style>
    .imgbox_adoption{
      margin-right: 0px;
    }
  </style>

  <script>
    (function(global){
      function Discussion(){}
      Discussion.issue_id = (function(){ var arr = location.pathname.split("/"); arr.pop(-1); return arr.pop(-1); }());
      Discussion.validate = function(){
      }
      Discussion.append = function(message, current_user){

        var msg_dom =
        '<li style="display:none;" class="me" data-n="'+message.id+'">'+
          '<dl>'+
            '<dt>'+
              '<div class="thmb" style="background-image:url('+current_user.picture+');"></div>'+
              '<a href="#">'+current_user.full_name+'</a>さん'+
            '</dt>'+
            '<dd>'+
              '<div class="message">'+
                '<p class="date">投稿日時 ：'+message.value.created_at+'</p>'+
                '<p class="txt">'+message.value.body+
                '</p>'+
              '</div>'+
            '</dd>'+
            '<br style="clear:both">'+
          '</dl>'+
        '</li>';

        $(".messagearea > ul").append(msg_dom);
        $("[data-n="+message.id+"]").show('slow');
      }

      global.Discussion = Discussion;
      return global;
    }(window));



    $(function(){
      honeybase.current_user(function(flag, current_user){
        var pubsub = honeybase.pubsub("discussion"+Discussion.issue_id);
        pubsub.sub(function(data){
          Discussion.append(data, current_user);
        });

        // chat投稿
        $("textarea[name=body]").keypress(function(e){ if(e.which == 13){ $("#submit").trigger('click'); } });
        $("#submit").click(function(e){
          MessageDB.insert({body: $("textarea[name=body]").val(), sender_id:current_user.id, issue_id:Discussion.issue_id, deleted:false }, function(flag, data){
            pubsub.pub(data);
          });
        });

        // メンバー選択
        $(".imgbox").click(function(e){
          $(this).removeClass("imgbox");
          $(this).html('<span class="msk"><i class="fa fa-check-circle"></i></span>');

          $(this).addClass("imgbox_adoption");
          $(".imgbox_adoption").one("click", function(e){
            $(this).removeClass("imgbox_adoption");
            $(this).empty();

            $(this).addClass("imgbox");
          });
        });

        // 除外
        $("#delete").click(function(e){
          if( $(".imgbox_adoption").length > 0 ) {
            $.each($(".imgbox_adoption"), function(i, item){
              var executor_id = $(item).data('n');
              IssueExecutorDB.select({executor_id:executor_id, issue_id:Discussion.issue_id}).done(function(flag, issue_executors){
                if(flag){
                  IssueExecutorDB.delete(parseInt(issue_executors[0].id), function(flag){
                    if(flag) {
                      $('[data-n='+executor_id+']').remove();
                      Notice.success("メンバー取り消し完了");
                    } else Notice.error("通信エラー");
                  });
                } else Notice.error("通信エラー");
              });
            });
          } else {
            Notice.error("メンバーを選んでください");
          }
        });

      });
    });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
