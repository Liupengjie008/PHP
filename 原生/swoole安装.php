基础环境：
	1.CentOS7
	2.lamp：PHP7、mysql5.7（www.lnmp.org）
	3.安装swoole2.0.6

swoole安装步骤：

	1.下载swoole的源码包、解压
		#进入/usr/local/src/
		[root@localhost ~]# cd /usr/local/src/

		#下载swoole源码包
		[root@localhost src]# wget -c https://github.com/swoole/swoole-src/archive/v2.0.6.tar.gz

		#解压
		[root@localhost src]# tar -zxvf v2.0.6.tar.gz

		#进入解压后的文件夹
		[root@localhost src]# tcd swoole-src-2.0.6/

	2.编译&安装
		#使用phpize来生成php编译配置
		[root@localhost swoole-src-2.0.6]# phpize

		#查看php-config路径
		[root@localhost swoole-src-2.0.6]# find / -name  php-config
		/usr/local/php/bin/php-config

		#./configure 来做编译配置检测
		[root@localhost swoole-src-2.0.6]# ./configure --with-php-config=/usr/local/php/bin/php-config

		#make进行编译，make install进行安装
		[root@localhost swoole-src-2.0.6]# make && make install

		#如果正确安装完成，会出现以下内容
		Installing shared extensions:     /usr/local/php/lib/php/extensions/no-debug-non-zts-20160303/
		#表示，在 /usr/local/php/lib/php/extensions/no-debug-non-zts-20160303/ 目录中，成功生成了 swoole.so 文件

	3.修改配置文件
		#查看php.ini路径
		[root@localhost swoole-src-2.0.6]# sudo find / -name 'php.ini' 
		/usr/local/php/etc/php.ini

		#用vi编辑器、编辑php.ini
		[root@localhost swoole-src-2.0.6]# vi /usr/local/php/etc/php.ini
		
		#在php.ini尾部添加php拓展swoole配置
		; Enable swoole extension module
		extension=swoole.so

	4.重启服务
		[root@localhost swoole-src-2.0.6]# lnmp restart
		+-------------------------------------------+
		|    Manager for LNMP, Written by Licess    |
		+-------------------------------------------+
		|              https://lnmp.org             |
		+-------------------------------------------+
		Stoping LNMP...
		Stoping nginx...  done
		Shutting down MySQL.. SUCCESS! 
		Gracefully shutting down php-fpm . done
		Starting LNMP...
		Starting nginx...  done
		Starting MySQL. SUCCESS! 
		Starting php-fpm  done

	5.通过php -m来查看是否成功加载了swoole
		[root@localhost swoole-src-2.0.6]# php -m
		[PHP Modules]
		bcmath
		Core
		ctype
		...
		...
		...
		standard
		swoole
		sysvsem
		...
		...
		...













