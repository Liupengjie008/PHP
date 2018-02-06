<?php

	//创建websocket服务器对象，监听0.0.0.0:9502端口
	$ws = new swoole_websocket_server("0.0.0.0", 9502);

	/*
		WebSocket服务器是建立在Http服务器之上的长连接服务器，客户端首先会发送一个Http的请求与服务器进行握手。握手成功后会触发onOpen事件，表示连接已就绪，onOpen函数中可以得到$request对象，包含了Http握手的相关信息，如GET参数、Cookie、Http头信息等。

		建立连接后客户端与服务器端就可以双向通信了。

		客户端向服务器端发送信息时，服务器端触发onMessage事件回调
		服务器端可以调用$server->push()向某个客户端（使用$fd标识符）发送消息
		服务器端可以设置onHandShake事件回调来手工处理WebSocket握手
	*/

	//监听WebSocket连接打开事件
	$ws->on('open', function ($ws, $request) {
	    var_dump($request->fd, $request->get, $request->server);
	    $ws->push($request->fd, "hello, welcome\n");
	});

	//监听WebSocket消息事件
	$ws->on('message', function ($ws, $frame) {
	    echo "Message: {$frame->data}\n";
	    $ws->push($frame->fd, "server: {$frame->data}");
	});

	//监听WebSocket连接关闭事件
	$ws->on('close', function ($ws, $fd) {
	    echo "client-{$fd} is closed\n";
	});

	$ws->start();

	/*
		#运行程序
		[root@localhost swoole]# php ws_server.php 

		
		#Comet
		WebSocket服务器除了提供WebSocket功能之外，实际上也可以处理Http长连接。只需要增加onRequest事件监听即可实现Comet方案Http长轮询。
	*/

?>