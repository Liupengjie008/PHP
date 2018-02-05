#!/usr/local/bin/php -q
<?php
// 实例三、直接使用TCP传输数据
// 创建tcp_test.php
use Workerman\Worker;
// require_once __DIR__ . '/Workerman/Autoloader.php';
require_once  str_replace('/','\\','../vendor/workerman/workerman-for-win/Autoloader.php');

// 创建一个Worker监听2347端口，不使用任何应用层协议
$tcp_worker = new Worker("tcp://0.0.0.0:2347");

// 启动4个进程对外提供服务
$tcp_worker->count = 4;

// 当客户端发来数据时
$tcp_worker->onMessage = function($connection, $data)
{
    // 向客户端发送hello $data
    $connection->send('hello ' . $data);
};

// 运行worker
Worker::runAll();


/*
命令行运行

php tcp_test.php start

测试：命令行运行
(以下是linux命令行效果，与windows下效果有所不同)
telnet 127.0.0.1 2347
Trying 127.0.0.1...
Connected to 127.0.0.1.
Escape character is '^]'.
tom
hello tom
注意：
1、如果出现无法访问的情况，请参照手册常见问题-连接失败一节排查。
2、服务端是裸tcp协议，用websoket、http等其它协议无法直接通讯。

*/