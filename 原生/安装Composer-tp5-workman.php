#Composer安装
[root@localhost ~]# curl -sS https://getcomposer.org/installer | php
All settings correct for using Composer
Downloading...

Composer (version 1.6.3) successfully installed to: /root/composer.phar
Use it: php composer.phar

[root@localhost ~]# ls
anaconda-ks.cfg  composer.phar  lnmp1.4  lnmp1.4.tar.gz  lnmp-install.log

[root@localhost ~]# mv composer.phar /usr/local/bin/composer
mv：是否覆盖"/usr/local/bin/composer"？ y

#切换到WEB根目录下面并执行下面的命令、安装thinkphp5
[root@localhost default]# composer create-project topthink/think tp5

#通过 composer 安装 Workerman
[root@localhost default]# cd tp5/
[root@localhost tp5]# composer require topthink/think-worker

#安装成功查看
[root@localhost tp5]# cd vendor/
[root@localhost vendor]# ls
autoload.php  composer  topthink  workerman
