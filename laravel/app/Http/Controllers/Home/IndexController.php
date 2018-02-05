<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{	
	// http://localhost/laravel/public/index.php/home/index/index?id=1&name=hello
    public function index(Request $request){
    	echo $request->input('id'); // 输出 1
        echo $request->input('name'); // 输出 hello
    }
    // http://localhost/laravel/public/index.php/home/index/index_copy/1?name=hello
    public function index_copy($id,Request $request){
    	echo $id; // 输出 1
        echo $request->input('name'); // 输出 hello
    }

    public function view(){
	    $assign = [
	        'title' => '文章的标题',
	        'content' => '文章的内容',
	    ];
	    $title = 'title';
	    // echo '<pre>';
	    // var_dump($assign);die;
	    // return view('home.index.view', $assign);
	    return view('home/index/view', $assign); //方法二
	}
}
