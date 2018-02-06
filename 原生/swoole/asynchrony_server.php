<?php
	
	/*
		执行异步任务
		在Server程序中如果需要执行很耗时的操作，比如一个聊天服务器发送广播，Web服务器中发送邮件。如果直接去执行这些函数就会阻塞当前进程，导致服务器响应变慢。

		Swoole提供了异步任务处理的功能，可以投递一个异步任务到TaskWorker进程池中执行，不影响当前请求的处理速度。

		基于TCP服务器，只需要增加onTask和onFinish2个事件回调函数即可。另外需要设置task进程数量，可以根据任务的耗时和任务量配置适量的task进程。
	*/

	$serv = new swoole_server("127.0.0.1", 9501);

	//设置异步任务的工作进程数量
	$serv->set(array('task_worker_num' => 4));

	$serv->on('receive', function($serv, $fd, $from_id, $data) {
		$serv->send($fd, "Server: ".$data);
	    //投递异步任务
	    $task_id = $serv->task($data);
	    echo "Dispath AsyncTask: id=$task_id\n";
	});

	//处理异步任务
	$serv->on('task', function ($serv, $task_id, $from_id, $data) {
	    echo "New AsyncTask[id=$task_id]".PHP_EOL;
	    //返回任务执行的结果
	    $serv->finish("$data -> OK");
	});

	//处理异步任务的结果
	$serv->on('finish', function ($serv, $task_id, $data) {
	    echo "AsyncTask[$task_id] Finish: $data".PHP_EOL;
	});

	$serv->start();

	/*
		调用$serv->task()后，程序立即返回，继续向下执行代码。onTask回调函数Task进程池内被异步执行。执行完成后调用$serv->finish()返回结果。
	*/

	/*
		#窗口1，执行程序: 
		[root@localhost swoole]# php tcp_server.php

		启动成功后可以使用 netstat 工具看到，已经在监听9501端口。这时就可以使用telnet/netcat工具连接服务器。

		#新启窗口2
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

		#窗口1 返回
		Server: hello

		#窗口2 返回
		Dispath AsyncTask: id=0
		New AsyncTask[id=0]
		AsyncTask[0] Finish: hello
		 -> OK

	*/

?>