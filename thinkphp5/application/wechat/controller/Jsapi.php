<?php
namespace app\wechat\controller;
use think\Controller;
//jsapi
class Jsapi extends Controller
{
    public function index(){
        $str =  'wechat pay';

        $this->assign('str',$str);
        echo $this->fetch();    //等于ThinkPHP3的$this->display();
    }

}

