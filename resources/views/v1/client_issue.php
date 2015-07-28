<?php include __PUBLIC__ . '/assets/header.php' ?>


  <!-- common -->
  <div class="common cf">

  <!----------------------- any contents ----------------------->

  <!-- issuesContents -->
  <article class="issuesContents">

    <!-- mv -->
    <section class="mvArea">
      <div class="mv">
        <div class="visual" style="background-image:url(../../assets/img/issues/fv_cover02.png);"></div>
      </div>
    </section>
    <!-- //mv -->

    <!-- issues_home -->
    <h2>ボードメンバーをアサインしましょう</h2>
    <section class="issues_home w980">
      <!-- result -->
      <h3><span style="background-image:url(../../assets/img/common/sample03.jpg);"></span><?php echo $issue['title']; ?></h3>
      <div class="result">
        <p class="check"><i class="fa fa-check-circle"></i>大カテゴリー<?php echo $issue['category1']; ?></p>
        <p class="check"><i class="fa fa-check-circle"></i>小カテゴリー<?php echo $issue['category2']; ?></p>
        <p class="date">投稿日時 ：<?php echo $issue['created_at']; ?></p>
        <p class="txt"><?php echo $issue['body']; ?></p>
        <p class="evocation">左のチェックを入れてメンバーアサインしてください。(最大3人)</p>
      </div>
      <!-- //result -->

      <!-- expert -->
      <div class="expert form05 form06">
        <ul><?php $i=0; foreach($experts as $expert) { $i++; echo
          '<li>'.
            '<dl>'.
              '<p class="check"><input type="checkbox" name="expert_checkbox" id="checkbox-'.$i.'" data-n="'.$expert['id'].'" /><label for="checkbox-'.$i.'"></label></p>'.
              '<dt>'.$expert['description'].'</dt>'.
              '<dd>'.
                '<p class="thmb" style="background-image:url('.$expert['picture'].');"></p>'.
                '株式会社ダミー<br>'.
                '<a href="">'.$expert['full_name'].'</a>さん </dd>'.
              '<br style="clear:both">'.
            '</dl>'.
          '</li>' ;} ?>
        </ul>
        <div class="cv_btn">
          <button  class="btn02_4">選択したメンバーでディスカッションをはじめる</button>
        </div>
      </div>
      <!-- //expert -->
    </section>
    <!-- //issues_home -->

  </article>
  <!-- //issuesContents -->

  <!----------------------- //any contents ----------------------->

  </div>
  <!-- //common -->


  <script>
    (function(global){
      function ClientIssue(){}
      ClientIssue.issue_id = (function(){ var arr = location.pathname.split("/"); return arr.pop(-1); }());
      ClientIssue._a = function(){
      }

      global.ClientIssue = ClientIssue;
      return global;
    }(window));

    $(function(){
      honeybase.current_user(function(flag, current_user){


        /* discussionの開始 */
        $(".btn02_4").click(function(e){
          $.each($("input[name=expert_checkbox]:checked"), function(i, item){
            var discusser_id = $(item).data('n');
            var pubsub = honeybase.pubsub("notification"+discusser_id);
            pubsub.pub({date:Date.now()});
            NotificationDB.insert({user_id: discusser_id}, function(flag, n){
              if(flag) ;
              else ;
            });

            IssueDiscusserDB.insert({discusser_id:discusser_id, issue_id:ClientIssue.issue_id}, function(flag, issue_executor){
              if(flag) {
                AI.redirect("/issues/"+ClientIssue.issue_id+"/discussion");
              } else Notice.error("通信エラー");
            });
          });
        });

      });
    });
  </script>
<?php include __PUBLIC__ . '/assets/footer.php'; ?>
