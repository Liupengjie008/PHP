<?php

	//创建Server对象，监听 127.0.0.1:9502端口，类型为SWOOLE_SOCK_UDP
	$serv = new swoole_server("127.0.0.1", 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP); 

	// UDP服务器与TCP服务器不同，UDP没有连接的概念。启动Server后，客户端无需Connect，直接可以向Server监听的9502端口发送数据包。对应的事件为onPacket。

	//监听数据接收事件，$clientInfo是客户端的相关信息，是一个数组，有客户端的IP和端口等内容，
	$serv->on('Packet', function ($serv, $data, $clientInfo) {
		// 调用 $serv->sendto 方法向客户端发送数据
	    $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server ".$data);
	    var_dump($clientInfo);
	});

	//启动服务器
	$serv->start(); 


	/*
		#启动服务
		[root@localhost swoole]# php udp_server.php 
		
		UDP服务器可以使用netcat -u 来连接测试
		
		#新启窗口 ( netcat 命令在Linux下是 nc )
		[root@localhost ~]# nc -u 127.0.0.1 9502
		hello
		Server hello

	*/



?>