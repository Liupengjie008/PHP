<?php
namespace app\phpanalysis\controller;

use think\phpanalysis\phpanalysis;
//中文分词demo
class Index
{
    public function index()
    {
        echo "thinkphp5";

        $t1 = $ntime = microtime(true);
		$endtime = '未执行任何操作，不统计！';

        $str = '我新哥是个大帅哥';
        $memory_info = '';
		$this->print_memory('没任何操作', $memory_info);
        if($str != '')
		{
		    //岐义处理
		    $do_fork = true;
		    //新词识别
		    $do_unit = true;
		    //多元切分
		    $do_multi = false;
		    //词性标注
		    $do_prop = false;
		    //是否预载全部词条
		    $pri_dict = false;
		    
		    $tall = microtime(true);
		    
		    //初始化类
		    PhpAnalysis::$loadInit = false;
		    $pa = new PhpAnalysis('utf-8', 'utf-8', $pri_dict);
		    $this->print_memory('初始化对象', $memory_info);
		    
		    //载入词典
		    $pa->LoadDict();
		    $this->print_memory('载入基本词典', $memory_info);    
		        
		    //执行分词
		    $pa->SetSource($str);
		    $pa->differMax = $do_multi;
		    $pa->unitWord = $do_unit;
		    
		    $pa->StartAnalysis( $do_fork );
		    $this->print_memory('执行分词', $memory_info);
		    
		    //分词结果
		    $okresult = $pa->GetFinallyResult(' ', $do_prop);
		    $this->print_memory('输出分词结果', $memory_info);
		    
		    //自动识别词
		    $pa_foundWordStr = $pa->foundWordStr;
		    
		    $t2 = microtime(true);
		    //总用时
		    $endtime = sprintf('%0.4f', $t2 - $t1);
		    //字符串长度
		    $slen = strlen($str);
		    $slen = sprintf('%0.2f', $slen/1024);
		    
		    $pa = '';
		    
		}

		dump($okresult);
		echo '<textarea name="result" id="result" style="width:98%;height:120px;font-size:14px;color:#555">'.$okresult.'</textarea>
<br /><br />
<b>调试信息：</b>
<hr />
<font color="blue">字串长度：</font>'.$slen.'K <font color="blue">自动识别词：</font>'.$pa_foundWordStr.'<br />
<hr />
<font color="blue">内存占用及执行时间：</font>(表示完成某个动作后正在占用的内存)<hr />
'.$memory_info.'
总用时： '.$endtime.' 秒
</td>
</tr>';

    }


    function print_memory($rc, &$infostr)
	{
	    global $ntime;
	    $cutime = microtime(true);
	    $etime = sprintf('%0.4f', $cutime - $ntime);
	    $m = sprintf('%0.2f', memory_get_usage()/1024/1024);
	    $infostr .= "{$rc}: &nbsp;{$m} MB 用时：{$etime} 秒<br />\n";
	    $ntime = $cutime;
	}


}
