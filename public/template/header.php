<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
  <title>Skill shared｜マーケティング特化型エキスパートマッチングサービス</title>
  <meta name="keywords" content=",,,,,,">
  <meta name="description" content="">
  <meta name="robots" content="index,follow">
  <meta property="og:title" content="Skill shared｜マーケティング特化型エキスパートマッチングサービス">
  <meta property="og:type" content="website">
  <meta property="og:description" content="">
  <meta property="og:url" content="http://">
  <meta property="og:image" content="ogp">
  <meta property="og:site_name" content="skill_shared">
  <meta property="fb:app_id" content="app_id">
  <link rel="shortcut icon" href="/assets/img/common/favicon.ico">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <!--css-->
  <link rel="stylesheet" href="http://techfund.jp/peastrap/css/peastrap.css">
  <link rel="stylesheet" href="/assets/css/base.css">
  <link rel="stylesheet" href="/assets/css/page.css">
  <!--script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script-->
  <!--ex-->
    <!--drawer-->
    <link rel="stylesheet" href="/assets/ex/drawer/slidebars.css">
    <!-- jQuery -->
    <script src="/assets/ex/drawer/jquery-1.11.0.min.js"></script>
    <script src="/assets/ex/drawer/slidebars.js"></script>
    <script>
      (function($) {
        $(document).ready(function() {
          $.slidebars();
        });
      }) (jQuery);
    </script>
</head>


<body>
<?php
  function isAdmin(){
    return strpos($_SERVER["REQUEST_URI"], "/admin") !== false;
  }

  function isProfileEdit(){
    return strpos($_SERVER["REQUEST_URI"], "/my/profile/edit") !== false;
  }

  function isLoggedIn() {
    return (strpos($_SERVER["REQUEST_URI"], "/my") !== false) || isAdmin();
  }

?>
	<div class="sb-slidebar sb-right">
    <ul><?php if( !isAdmin() ){
      if ( isset($current_user) && $current_user['type']) {$type = $current_user['type'];} else {$type = "client";}
      echo
      '<a href="/client/issues/new"><li><img src="" />仕事を依頼する</li></a>'.
      '<a href="/'.$type.'/issues"><li><img src="" />アサイン中案件</li></a>'.
      '<a href="/about"><li class="menu03"><i class="fa fa-bell-o"></i>サービスについて</li></a>'.
      '<li><span>notification</span><ul id="notification_list" style="display:none;"></ul></li>'; } else { echo
      '<a href="/admin"><li><img src="" />案件・専門家管理</li></a>'.
      '<a href="/admin/analytics"><li><img src="" />統計情報</li></a>'; } ?>
  	</ul>
  </div>

	<!-- header -->
	<header class="sb-slide">
  	<!--p class="toggle sb-toggle-left">＝</p-->
    <section class="toparea">
    	<div class="w980 cf">
      	<div class="logo">
        	<a href="http://skillshared.techfund.jp/"><img src="/assets/svg/logo.svg" /></a>
        	<h1>3つの質問であなたに最適なマーケティングパートナーをアサイン！！</h1>
        </div>
        <a href="#">
        <?php if( !isset($current_user) ){
          echo '
        	<div class="h_menu" id="login_modal_btn"><i class="fa fa-user-plus"></i>会員登録 ｜ エキスパート登録</div>
          ';
        } else {
          echo '
          <div class="h_menu" onclick="honeybase.logout(function(flag){location.href=\'/\';});">ログアウト</div>
          ';
        }
        ?>
        </a>
      </div>
    </section>
    <section class="bottomarea">
      <div class="g_menu">
        <ul><?php if( !isAdmin() ){
          if ( isset($current_user) && $current_user['type']) {$type = $current_user['type'];} else {$type = "client";}
          echo
          '<a href="/client/issues/new"><li class="menu01"><i class="fa fa-file-text"></i>仕事を依頼する</li></a>'.
          '<a href="/'.$type.'/issues"><li class="menu02"><i class="fa fa-eye"></i>アサイン中案件</li></a>'.
          '<a href="/about"><li class="menu03"><i class="fa fa-bell-o"></i>サービスについて</li></a>'.
          '<li id="notification_count" class="menu04 sb-toggle-right"><a><p>-</p><i class="fa fa-comments"></i></a></li>'; } else { echo
          '<a href="/admin"><li class="menu01"><img src="" />案件・専門家管理</li></a>'.
          '<a href="/admin/analytics"><li class="menu02"><img src="" />統計情報</li></a>'; } ?>
        </ul>
      </div>
    </section>
    <section class="pankuzuarea">
    	<ul class="cf">
      	<li class="icon"><a href="#"><i class="fa fa-home"></i></a></li>
      </ul>
    </section>
  </header>
