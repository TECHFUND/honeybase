<?php namespace App\Controller;

use App\Controller\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\MysqlAdaptor;

use App\Util\NuLog;


class HomeController extends Controller {

  public function index(Request $request) {
    return view('index');
    // viewの読み込みはこれで追加
    // http://qiita.com/sogawa@github/items/644710df8d58d54f2665
  }
}
