<?php
namespace app\wechat\controller;

use think\Controller;
use think\Cache;
use think\Db;
use think\Url;
use think\Request;

/**
* 
*/
class Imagetext extends Controller
{
	public function index(){
		return $this->fetch();
	}
	
	public function add(){
		if(request()->isPost()){
			
			$content = input('post.content');
			// dump($content);
			echo $content;
			die;
		}
		return $this->fetch();
	}

}