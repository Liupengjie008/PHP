<?php

	//创建Server对象，监听 127.0.0.1:9501端口
	$serv = new swoole_server("127.0.0.1", 9501); 

	// 服务器可以同时被成千上万个客户端连接，$fd就是客户端连接的唯一标识符
	//监听连接进入事件，当有新的TCP连接进入时会执行onConnect事件回调
	$serv->on('connect', function ($serv, $fd) {  
	    echo "Client: Connect.\n";
	});

	//监听数据接收事件，当某个连接向服务器发送数据时会回调onReceive函数
	$serv->on('receive', function ($serv, $fd, $from_id, $data) {
	    $serv->send($fd, "Server: ".$data);
	    //遍历所有连接,将接到的消息广播出去
	    // foreach($serv->connections as $f){  
	    //     $serv->send($f, $data);  
	    // }  
	});

	//监听连接关闭事件，客户端主动断开连接，此时会触发onClose事件回调
	$serv->on('close', function ($serv, $fd) {
	    echo "Client: Close.\n";
	});

	//启动服务器
	$serv->start(); 



	/*
		执行程序: 
		[root@localhost swoole]# php tcp_server.php

		启动成功后可以使用 netstat 工具看到，已经在监听9501端口。这时就可以使用telnet/netcat工具连接服务器。

		#新启窗口
		#在Linux下，使用netstat -an | grep 端口，查看端口状态
		[root@localhost ~]# netstat -an | grep 9501
		tcp        0      0 127.0.0.1:9501          0.0.0.0:*               LISTEN 
		
		#使用telnet/netcat工具连接服务器
		[root@localhost ~]# telnet 127.0.0.1 9501
		Trying 127.0.0.1...
		Connected to 127.0.0.1.
		Escape character is '^]'.

		#输入lello
		hello

		#返回
		Server: hello

		#退出telnet，用CTRL+]键
		^]

		#输入quit
		telnet> quit
		Connection closed.
		
	*/



?>

