#!/usr/local/bin/php -q
<?php  
  
//创建websocket服务器对象，监听0.0.0.0:9502端口  
$ws = new swoole_websocket_server("0.0.0.0",9502);  
  
//监听WebSocket连接打开事件  
$ws->on('open', function ($ws, $request) {  
    var_dump($request->fd, $request->get, $request->server);  
    $ws->push($request->fd, '{"data":"socket server connected"}');  
});  
  
//监听WebSocket消息事件  
$ws->on('message', function ($ws, $frame) {  
    //echo "Message: {$frame->data}\n";  
    echo "<pre>";  
    print_r($frame);  
  
    //遍历所有连接,将接到的消息广播出去  
    foreach($ws->connections as $fd){  
        $ws->push($fd, "{$frame->data}");  
    }  
    //$ws->push($frame->fd, "{$frame->data}");  
});  
  
//监听WebSocket连接关闭事件  
$ws->on('close', function ($ws, $fd) {  
    echo "client-{$fd} is closed\n";  
});  
  
$ws->start();  
  
?>  