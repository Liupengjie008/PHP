<?php
	
	/*
		0.0.0.0 表示监听所有IP地址，一台服务器可能同时有多个IP，如127.0.0.1本地回环IP、192.168.1.100局域网IP、210.127.20.2 外网IP，这里也可以单独指定监听一个IP
		9501 监听的端口，如果被占用程序会抛出致命错误，中断执行。
	*/

	$http = new swoole_http_server("0.0.0.0", 9501);

	/*
		Http服务器只需要关注请求响应即可，所以只需要监听一个onRequest事件。当有新的Http请求进入就会触发此事件。事件回调函数有2个参数，一个是$request对象，包含了请求的相关信息，如GET/POST请求的数据。

		另外一个是response对象，对request的响应可以通过操作response对象来完成。$response->end()方法表示输出一段HTML内容，并结束此请求。

	*/

	$http->on('request', function ($request, $response) {
		$request->get = isset($request->get)?$request->get:[]; 
		$request->post = isset($request->post)?$request->post:[];
	    var_dump($request->get,$request->post);
	    $response->header("Content-Type", "text/html; charset=utf-8");
	    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
	});

	$http->start();

	/*
		#启动服务
		[root@localhost swoole]# php web_server.php 

		#可以打开浏览器，访问http://127.0.0.1:9501查看程序的结果。 http://[服务器ip地址]:9501

		#也可以使用apache ab工具对服务器进行压力测试
	*/

?>