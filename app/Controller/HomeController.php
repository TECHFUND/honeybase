<?php namespace App\Controller;

use App\Controller\Controller;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\MysqlAdaptor;

use App\Util\NuLog;


class HomeController extends Controller {

  public function index(Request $request) {
    return view('index');
  }
}
