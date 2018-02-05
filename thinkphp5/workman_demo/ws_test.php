#!/usr/local/bin/php -q
<?php
// 实例二、使用WebSocket协议对外提供服务
// 创建ws_test.php文件
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Worker;
// require_once __DIR__ . '/Workerman/Autoloader.php';
require_once  str_replace('/','\\','../vendor/workerman/workerman-for-win/Autoloader.php');

// 注意：这里与上个例子不通，使用的是websocket协议
$ws_worker = new Worker("websocket://0.0.0.0:2000");

// 启动4个进程对外提供服务
$ws_worker->count = 4;

// 当收到客户端发来的数据后返回hello $data给客户端
$ws_worker->onMessage = function($connection, $data)
{
    // 向客户端发送hello $data
    var_dump($connection);
    echo $data;
    $connection->send($data);

};

// 设置连接的onClose回调
$connection->onClose = function($connection) //客户端主动关闭
{
    echo "connection closed\n";
};

// 运行worker
Worker::runAll();



/*

命令行运行

php ws_test.php start

测试

打开chrome浏览器，按F12打开调试控制台，在Console一栏输入(或者把下面代码放入到html页面用js运行)

// 假设服务端ip为127.0.0.1
ws = new WebSocket("ws://127.0.0.1:2000");
ws.onopen = function() {
    alert("连接成功");
    ws.send('tom');
    alert("给服务端发送一个字符串：tom");
};
ws.onmessage = function(e) {
    alert("收到服务端的消息：" + e.data);
};
注意：
1、如果出现无法访问的情况，请参照手册常见问题-连接失败一节排查。
2、服务端是websocket协议，只能用websocket协议通讯，用http等其它协议无法直接通讯。

*/