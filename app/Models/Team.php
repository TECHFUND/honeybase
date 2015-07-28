<?php namespace App\Models;

use App\Util\NuLog;
use App\Models\User;

class Team {
  public static function all($join = false){

    $db = new MysqlAdaptor();
    $teams = [];
    if($join){
      $res = $db->joined_select("teams", "owner_id", "users", "id", []); //teams.user_idとusers.idでjoinして全件取得
      $teams = $res['data'];
    } else {
      $res = $db->select("teams", []);
      $teams = $res['data'];
    }
    return $teams;
  }

  public static function find($id){
    $db = new MysqlAdaptor();
    $res = $db->select("teams", ["id"=>$id]);
    if($res['flag']){
      return $res['data'][0];
    } else {
      return null;
    }
  }

  public static function all_with_user(){
    $teams = [];
    foreach(self::all(false) as $team){
      $user = User::search(["id" => $team["owner_id"]])[0];
      unset($user['id']);
      array_push($teams, array_merge($team, $user));
    }
    return $teams;
  }


      /*

    function team_array($id, $name, $pic, $cover, $link, $description){
      return ["id"=>$id,"name"=>$name,"owner_picture"=>$pic,
        "team_cover"=>$cover,"link"=>$link, "description"=>$description];
    }
    $dummy = [
      team_array("0", "psk", "http://graph.facebook.com/peaske.kawahara/picture",
        "https://scontent.xx.fbcdn.net/hphotos-xtf1/v/t1.0-9/10366018_659146387497872_1268359498947561773_n.jpg?oh=33997b90ba9187290cde758b2104855b&oe=55EFB446",
        "", "")
      // memberはusers.team_id == teams.idなjoinでとってくる
      ["id"=>"0", "name"=>"RAD", "owner_picture"=>"http://graph.facebook.com/takac.radcliffe/picture", "team_cover"=>"https://scontent.xx.fbcdn.net/hphotos-xaf1/t31.0-8/p180x540/465688_490012277696369_843501654_o.jpg"],
      ["id"=>"0", "name"=>"peaske", "owner_picture"=>"http://www.ta-24.com/onikasu/torukoga/ameblo/message/img/120217_peaske/photo_1.jpg", "team_cover"=>"http://livedoor.blogimg.jp/nezumisab/imgs/d/d/dda023f8.jpg"],
      ["id"=>"0", "name"=>"MTN", "owner_picture"=>"http://graph.facebook.com/milkywebboy/picture", "team_cover"=>"https://scontent.xx.fbcdn.net/hphotos-xaf1/v/t1.0-9/10703502_865753660131069_2492047187044493020_n.jpg?oh=4988f347a9215e54a78279f5f7b1fa06&oe=56050682"],
      ["id"=>"0", "name"=>"MTK", "owner_picture"=>"http://graph.facebook.com/motoki.ozawa/picture", "team_cover"=>"https://fbcdn-sphotos-a-a.akamaihd.net/hphotos-ak-xat1/v/t1.0-9/10690320_725112484233052_3283401270953745567_n.jpg?oh=333021b2763a318a6e252c1d6f7adc6e&oe=560B1985&__gda__=1442770010_8a6529c06408ee96fc1a67289254ee0a"],
      ["id"=>"0", "name"=>"OCHIN", "owner_picture"=>"http://graph.facebook.com/shogochiai/picture", "team_cover"=>"https://fbcdn-sphotos-g-a.akamaihd.net/hphotos-ak-xfp1/t31.0-8/893738_736985343020664_7177494223574045717_o.jpg"],
      ["id"=>"0", "name"=>"psk", "owner_picture"=>"http://graph.facebook.com/peaske.kawahara/picture", "team_cover"=>"https://scontent.xx.fbcdn.net/hphotos-xtf1/v/t1.0-9/10366018_659146387497872_1268359498947561773_n.jpg?oh=33997b90ba9187290cde758b2104855b&oe=55EFB446"],
      ["id"=>"0", "name"=>"RAD", "owner_picture"=>"http://graph.facebook.com/takac.radcliffe/picture", "team_cover"=>"https://scontent.xx.fbcdn.net/hphotos-xaf1/t31.0-8/p180x540/465688_490012277696369_843501654_o.jpg"],
      ["id"=>"0", "name"=>"peaske", "owner_picture"=>"http://www.ta-24.com/onikasu/torukoga/ameblo/message/img/120217_peaske/photo_1.jpg", "team_cover"=>"http://livedoor.blogimg.jp/nezumisab/imgs/d/d/dda023f8.jpg"],
      ["id"=>"0", "name"=>"MTN", "owner_picture"=>"http://graph.facebook.com/milkywebboy/picture", "team_cover"=>"https://scontent.xx.fbcdn.net/hphotos-xaf1/v/t1.0-9/10703502_865753660131069_2492047187044493020_n.jpg?oh=4988f347a9215e54a78279f5f7b1fa06&oe=56050682"],
      ["id"=>"0", "name"=>"MTK", "owner_picture"=>"http://graph.facebook.com/motoki.ozawa/picture", "team_cover"=>"https://fbcdn-sphotos-a-a.akamaihd.net/hphotos-ak-xat1/v/t1.0-9/10690320_725112484233052_3283401270953745567_n.jpg?oh=333021b2763a318a6e252c1d6f7adc6e&oe=560B1985&__gda__=1442770010_8a6529c06408ee96fc1a67289254ee0a"],
      ["id"=>"0", "name"=>"OCHIN", "owner_picture"=>"http://graph.facebook.com/shogochiai/picture", "team_cover"=>"https://fbcdn-sphotos-g-a.akamaihd.net/hphotos-ak-xfp1/t31.0-8/893738_736985343020664_7177494223574045717_o.jpg"]
    ];
      */


}
