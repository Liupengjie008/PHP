<?php

PHP CLI模式介绍及使用教程
PHP CLI：php-cli是php Command Line Interface 即PHP命令行接口。
在windows和linux下都是支持PHP-CLI模式。

PHP-CLI模式的优势及使用场合：
1.完全支持多线程
2.如上，可以实现定时任务
3.开发桌面应用就是使用PHP-CLI和GTK包
4.linux下用php编写shell脚本


以下是常用的php cli 命令：（在控制台输入）
        [Windows系统用php.exe]
    php -v    显示PHP 的版本
      例：php.exe -v
    php --ini     输出php.ini配置文件的信息
      例：php.exe --ini
    php --rf  function <name> 输出php 函数的信息，包括函数的参数
      例：php.exe --rf strstr
    php --ri  <extension> 输出php扩展的信息
      例：php.exe --ri
    php -i 输出php的详细信息，内容很多，相当于phpinfo();
      例：php.exe -i
    php -m 输出被编译的模块
      例：php.exe -m
    php --re 输出php扩展模块的信息，包块此模块对应的函数，此extension中定义的常量
      例：php.exe -re


用cli方式运行PHP程序

    新建一个cli.php文件

    第一行输入：

    #!C:\php\php.exe -q

      Linux环境第一行输入：#!/usr/local/bin/php –q

    此命令表示这是一个cli程序

    后输入php标签  

    <?php

        echo  "hello php cli"; 

    ?>

    然后在命令行模式输入 php.exe cli.php 
      (Windows系统下运行该脚本，则不需要设置文件属性，可以直接运行该脚本)
    Linux下输入 php cli.php 
      ( 不要忘了给该文件设置为可执行的权限：$ chmod 755 cli.php )

    完整程序如下：
    #!C:\php\php.exe -q  
  <?php  
     echo 'hello php cli';  
  ?>  


  从命令行获取参数
    通过 $_SERVER['argc'] 和 $_SERVER['argc']来传递参数

    #!C:\php\php.exe -q  
  <?php  
     echo "hello php cli\n";  
     var_dump($_SERVER['argc']);   //$_SERVER['argc']  为传递的参数的个数  
     var_dump($_SERVER['argv']);   //S_SERVER['argv']  为传递的参数的值，以数组表示  
  ?>  

    然后在命令行模式输入 php.exe cli.php one two three

    输出：
    hello php cli
  int(4)
  array(4) {
    [0]=>
    string(7) "cli.php"
    [1]=>
    string(3) "one"
    [2]=>
    string(3) "two"
    [3]=>
    string(5) "three"
  }

  
处理I/O通道

输入输出（I/O）通道这个思想来源于UNIX系统，UNIX系统提供3个文件句柄，用以从一个应用程序及用户终端发送和接收数据。

我们可以把一个脚本的输出重定向到一个文件：

php world.php > outputfile

如果是在UNIX系统下，也可以使用通道定向到另一个命令或应用程序中。例如：

php world.php | sort.

在PHP 5 CLI中，有一个文件流句柄，可以使用3个系统常量，分别为STDIN、STDOUT和STDERR。下面我们分别介绍。

（1）STDIN

STDIN全称为standard in或standard input，标准输入可以从终端取得任何数据。

格式：stdin (’php://stdin’)

下面的例子是显示用户输入：

#!/usr/local/bin/php -q
<?php 
    $file = file_get_contents("php://stdin", "r");
    echo $file;
?>

这段代码的工作原理与cat命令很相似，回转提供给它的所有输入。但是，这时它还不能接收参数。

STDIN是PHP的标准输入设备，利用它，CLI PHP脚本可以做更多的事情。如下面例子：

Windows系统：
    #!C:\php\php.exe -q
    <?php
        if(!defined("STDIN")){
            define("STDIN", fopen('php://stdin','r'));
        } 
        echo "hello,What is your name ?(Plase input):\n";
        $strName = fread(STDIN, 100); //从一个新行读入80个字符 
        echo 'Welcome '.$strName."\n";
    ?>

Linux系统：

    #!/usr/local/bin/php -q 
    <?php//UNIX平台下第一行应该为#!/usr/bin/php –q/* 如果STDIN未定义，将新定义一个STDIN输入流 */ 
        if(!defined("STDIN")) {
            define("STDIN", fopen('php://stdin','r'));
        } 
        echo "你好!你叫什么名字(请输入):\n";
        $strName = fread(STDIN, 100); //从一个新行读入80个字符 
        echo '欢迎你'.$strName."\n";
    ?>
该脚本执行后将显示：你好!你叫什么名字(请输入):
比如，输入 Murphy 之后，将显示：欢迎你 Murphy

（2）STDOUT

STDOUT全称为standard out或standard output，标准输出可以直接输出到屏幕（也可以输出到其他程序，使用STDIN取得），如果在PHP CLI模式里使用print或echo语句，则这些数据将发送到STDOUT。

格式：stdout ('php://stdout')

我们还可以使用PHP函数进行数据流输出。如下面例子：

#!/usr/local/bin/php –q
<?php 
    $STDOUT = fopen('php://stdout', 'w');
    fwrite($STDOUT,"Hello World"); 
    fclose($STDOUT);
?>

输出结果如下：Hello World

例如，echo和print命令打印到标准输出。 

#!/usr/local/bin/php –q
Output #1.
<?php
    echo "Output #2.";
    print "Output #3." 
?>

这将得到：
D:\WWW>php.exe cli.php
Output #1.
Output #2.Output #3.

说明：PHP标记外的新行已被输出，但是echo命令或print命令中没有指示换行。事实上，命令提示符重新出现在Output #2.Output #3. 所在的行中。PHP拥有的任何其他打印函数将会像此函数一样运行正常，任何写回文件的函数也是一样的。

#!/usr/local/bin/php -q 
<?php
    $STDOUT = fopen("php://stdout", "w");
    fwrite($STDOUT, "Output #1."); 
    fclose($STDOUT);
?>

以上代码将把php://stdout作为输出通道显式打开，并且php://output通常以与php://stdout相同的方法运行。

（3）STDERR

STDERR全称为standard error，在默认情况下会直接发送至用户终端，当使用STDIN文件句柄从其他应用程序没有读取到数据时会生成一个“stdin.stderr”。

格式：stderr ('php://stderr')

下面的脚本表示如何把一行文本输出到错误流中。

#!/usr/local/bin/php –q
<?php 
    $STDERR = fopen('php://stderr', 'w');
    fwrite($STDERR,"There was an Error"); 
    fclose($STDERR);
?>

PHP 5.2可以直接使用STDOUT作为常量，而不是定义上面使用的变量$STDOUT，为了兼容之前版本，我们仍使用了自定义变量，如果您使用的是PHP 5.2，则可以参考STDIN的第二个例子。


后台运行CLI

如果正在运行一个进程，而且在退出账户时该进程还不会结束，即在系统后台或背景下运行，那么就可以使用nohup命令。该命令可以在退出账户之后继续运行相应的进程。

nohup在英文中就是不挂起的意思（no hang up）。该命令的一般形式为：

nohup –f scriptname.php &

使用nohup命令提交作业，在默认情况下该作业的所有输出都被重定向到一个名为nohup.out的文件中，除非另外指定了输出文件。

nohup scriptname.php > log.txt &

这样，PHP CLI脚本执行后的结果将输出到log.txt中，我们可以使用tail命令查看该内容：

tail -n50 -f log.txt

现在再来实现两个例子，第一个是每隔10分钟自动生成一个静态HTML文件，并一直执行下去。脚本代码如下：

Windows系统：
    #!C:\php\php.exe -q
    <?php
        set_time_limit(0);
        while(true){
            @fopen("D:\WWW\article_".time().".html","w");
            sleep(6);
        }
    ?>

Linux系统：
    #! /usr/local/bin/php 
    <?php
        set_time_limit(0);
        while(true){
            @fopen("/usr/local/www/data-dist/content/ article_".time().".html","w");
            sleep(600);
        }
    ?>

保存并且退出vi编辑器，然后赋予genHTML.php文件可执行权限：

#>chmod 755 genHTML.php 然后让脚本在后台执行，执行如下命令：$nohup genHTML.php –f &执行上述命令后出现如下提示：[1] 16623

按回车键后将出现shell提示符。上面的提示就是说，所有命令执行的输出信息都会放到nohup.out文件中。

执行上面命令后，每隔10分钟就会在指定的目录生成指定的HTML文件，如article_111990120.html等文件。

如何终止CLI程序的后台运行呢？

可以使用kill命令来终止这个进程，终止进程之前要知道进程的PID号，即进程ID，我们使用ps命令：

www# ps PID TT STAT TIME COMMAND 561 v0 Is+ 0:00.00 /usr/libexec/getty Pc ttyv0 562 v1 Is+ 0:00.00 /usr/libexec/getty Pc ttyv1 563 v2 Is+ 0:00.00 /usr/libexec/getty Pc ttyv2 564 v3 Is+ 0:00.00 /usr/libexec/getty Pc ttyv3 565 v4 Is+ 0:00.00 /usr/libexec/getty Pc ttyv4 566 v5 Is+ 0:00.00 /usr/libexec/getty Pc ttyv5 567 v6 Is+ 0:00.00 /usr/libexec/getty Pc ttyv6 568 v7 Is+ 0:00.00 /usr/libexec/getty Pc ttyv7 16180 p0 I 0:00.01 su 16181 p0 S 0:00.06 _su (csh) 16695 p0 R+ 0:00.00 ps 16623 p0 S 0:00.06 /usr/local/bin/php /usr/local/www/data/genHTML.php 已经看到PHP的进程ID是：16623，于是再执行kill命令：$ kill -9 16623 [1]+ Killed nohup /usr/local/www/data/genHTML.php 这时该命令的进程就已经被终止了，再使用ps命令：$ ps PID TT STAT TIME COMMAND 82374 p3 Ss 0:00.17 -bash (bash) 82535 p3 R+ 0:00.00 ps

刚才的PHP CLI脚本已经没有了，如果直接运行ps命令无法看到进程，那么就结合使用ps & apos两个命令来查看。

注意：上面例子必须运行在UNIX或者Linux系统中，如FreeBSD、Redhat Linux等，在Windows环境不支持nohup命令。




