#!/usr/local/bin/php -q  
<?php
// 第一步 连接MySQL数据库
	/*mysqli_connect(host,username,password,dbname,port,socket);
		host	可选。规定主机名或 IP 地址。
		username	可选。规定 MySQL 用户名。
		password	可选。规定 MySQL 密码。
		dbname	可选。规定默认使用的数据库。
		port	可选。规定尝试连接到 MySQL 服务器的端口号。
		socket	可选。规定 socket 或要使用的已命名 pipe。
	*/
	$link = @mysqli_connect("localhost","root","root");

	//var_dump($link);

	// 第二步 判断是否连接成功
	// mysqli_connect_errno()	// 返回连接错误号 0 表示成功，大于0表示失败
	// mysqli_connect_error() 	// 返回连接的错误信息
	if(mysqli_connect_errno()>0){
		// 输出错误信息，终止程序
		exit('数据库连接失败！错误原因:'.mysqli_connect_error());
	}

	// 第三步 选择默认操作的数据库
	mysqli_select_db($link,'test');

	// 第四步 设置默认字符集
	mysqli_set_charset($link,'utf8');

	// 第五步 准备sql语句
	$sql = "INSERT into students VALUES (4,'ceshi2',2,2);";

	// 第六步 执行sql语句
	// 如果执行的是查询(select)，返回的是结果集,失败时布尔值;如果是增删改,返回的是布尔值
	$result = mysqli_query($link,$sql);	 

	// 第七步 判断结果集，并处理
	if($result && @mysqli_num_rows($result)>0){
		
		echo mysqli_num_rows($result);

	}

	// 第八步 关闭mysql连接，释放结果集
	@mysqli_free_result($result);		// 释放结果集
	mysqli_close($link);
