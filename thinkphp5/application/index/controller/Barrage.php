<?php
namespace app\index\controller;

use think\Controller;
use think\captcha;

class Barrage extends Controller
{
    public function index()
    {
    	return $this->fetch();
    }   

    
}