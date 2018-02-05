<?php
namespace app\index\controller;

use think\Url;

use think\phpanalysis\phpanalysis;

class Index
{

	public function index()
    {
    	echo "您好： " . cookie('user_name') . ', <a href="' . url('login/loginout') . '">退出</a>';
    }  

    

}



