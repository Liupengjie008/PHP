#!/usr/local/bin/php -q
<?php
// 实例一、使用HTTP协议对外提供Web服务
// 创建http_test.php文件（位置任意，能引用到Workerman/Autoloader.php即可，下同）
use Workerman\Worker;

// echo str_replace('/','\\',__DIR__.'../vendor/workerman/workerman-for-win/Autoloader.php');die;
require_once  str_replace('/','\\','../vendor/workerman/workerman-for-win/Autoloader.php');

// 创建一个Worker监听2345端口，使用http协议通讯
$http_worker = new Worker("http://0.0.0.0:2345");

// 启动4个进程对外提供服务
$http_worker->count = 4;

// 接收到浏览器发送的数据时回复hello world给浏览器
$http_worker->onMessage = function($connection, $data)
{
    // 向浏览器发送hello world
    $connection->send('hello world');
};

// 运行worker
Worker::runAll();


/*

命令行运行（windows用户用 cmd命令行，下同）

php http_test.php start

测试

假设服务端ip为127.0.0.1

在浏览器中访问url http://127.0.0.1:2345

注意：
1、如果出现无法访问的情况，请参照手册常见问题-连接失败一节排查。
2、服务端是http协议，只能用http协议通讯，用websoket等其它协议无法直接通讯。

*/