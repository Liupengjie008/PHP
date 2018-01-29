<?php
namespace app\wechat\controller;

use think\Controller;
use think\Cache;
use think\Db;
/**
* 
*/
class User extends Controller
{
	
	public function index(){
      $info = Db::name('user')->field('openid,nickname,sex,city')->select();
        $this->assign('info',$info);
        return $this->fetch();    //等于ThinkPHP3的$this->display();
    }

    public function reply(){
      //调用客服回复接口回复用户
      $wechat = new wechat();
      $info = $wechat->custom_service('oe3nZvvHIIAh4NKgho7gqDReroe4','text','hello world!');
      // dump($info);

    }
}