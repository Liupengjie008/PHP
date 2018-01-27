<?php
namespace app\wechat\controller;

use think\Url;
use think\phpanalysis\phpanalysis;

class Index
{
    public function index()
    {	
    	//声明一个常量定义一个token值
       	define("TOKEN", "weixin");
       	//把获取到的http请求信息写入日志
       	$this->traceHttp();
       	//通过Wechat类， 创建一个对象
		$wechatObj = new wechat();
		if (isset($_GET['echostr'])) {			
			//调用valid()方法，进行token验证
		    $wechatObj->valid();
		}else{
			//调用wecat对象中的方法响应用户消息
		    $wechatObj->responseMsg();
		}
    }

    //写入日志
    function traceHttp()
	{
	    $content = date('Y-m-d H:i:s')."\nREMOTE_ADDR:".$_SERVER["REMOTE_ADDR"]."\nQUERY_STRING:".$_SERVER["QUERY_STRING"]."\n\n";
	    
	    if (isset($_SERVER['HTTP_APPNAME'])){   //SAE
	        sae_set_display_errors(false);
	        sae_debug(trim($content));
	        sae_set_display_errors(true);
	    }else {
	        $max_size = 100000;
	        $log_filename = "log.txt";
	        if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
	        file_put_contents($log_filename, $content, FILE_APPEND);
	    }
	}

	//微信自定义菜单
	public function menu()
	{	
		$access_token = $this->get_token();

		if($_POST){
			
	if(isset($_POST['do_submit'])) {
		for($i=0; $i<3; $i++) {		
			//指定下标
			$button = "button_{$i}";
			$sub_button = "sub_button_{$i}_0";
			$type = "type_{$i}";
			$urlkey = "urlkey_{$i}";

			//如果有子菜单
			if(trim($_POST[$sub_button])!="") {
				for($j=0; $j<5; $j++) {
					$sub_button = "sub_button_{$i}_{$j}";
					$sub_type = "type_{$i}_{$j}";
					$sub_urlkey = "urlkey_{$i}_{$j}";	

					if(trim($_POST[$sub_button]) != "") {
						$menuarr['button'][$i]['name']=$_POST[$button];

						if($_POST[$sub_type]=="key") {
							$menuarr['button'][$i]['sub_button'][$j]['type']= "click";
							$menuarr['button'][$i]['sub_button'][$j]['name'] = $_POST[$sub_button]; 
							$menuarr['button'][$i]['sub_button'][$j]['key'] =$_POST[$sub_urlkey];	
						}else if($_POST[$sub_type]=="url") {
							$menuarr['button'][$i]['sub_button'][$j]['type']= "view";
							$menuarr['button'][$i]['sub_button'][$j]['name'] = $_POST[$sub_button]; 
							$menuarr['button'][$i]['sub_button'][$j]['url'] =$_POST[$sub_urlkey];					
						}
					}				
				}

			}else {
								
				if(trim($_POST[$button])!="") {

					if($_POST[$type]=="key") {
						$menuarr['button'][$i]['type']= "click";
						$menuarr['button'][$i]['name'] = $_POST[$button]; 
						$menuarr['button'][$i]['key'] =$_POST[$urlkey];	
					}else if($_POST[$type]=="url") {
						$menuarr['button'][$i]['type']= "view";
						$menuarr['button'][$i]['name'] = $_POST[$button]; 
						$menuarr['button'][$i]['url'] =$_POST[$urlkey];					
					}
				}
			}
		}
	}

			$jsonmenu = $this->my_json_encode("text", $menuarr);

			$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
			$result = $this->https_request($url, $jsonmenu);
	
			var_dump($jsonmenu);
			echo '<br>';
			var_dump($result);

		}else{
			$url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$access_token;
			$result = $this->https_request($url);
			$arr_2 = json_decode($result, true);
			echo '<pre>';
			print_r($arr_2);
			echo '</pre>';
			return '<title>菜单管理</title>
			<h1>菜单管理</h1>
			<p>不需要设计的菜单留空就可以！</p>
			<table border="1">
			<form action="'.url('wechat/index/menu').'" method="post">
				<tr>
					<th>序号</th> <th>一级菜单</th> <th>子菜单</th>
				</tr>
				<tr>
					<th>菜单一:</th>
					<td>
						<input type="text" size="10" name="button_0"> <br>
						<input type="radio" name="type_0" checked value="url"> 	链接 <br>
						<input type="radio" name="type_0" value="key"> 模拟关键字<br>
						<input type="text" name="urlkey_0" size="30"> 
					</td>
					<td>
						<p>
						1 名称: <input type="text" size="10" name="sub_button_0_0"> &nbsp;
							<input type="radio" name="type_0_0" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_0_0" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_0_0" size="30"> 
						</p>


						<p>
						2  名称: <input type="text" size="10" name="sub_button_0_1"> &nbsp;
							<input type="radio" name="type_0_1" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_0_1" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_0_1" size="30"> 
						</p>

						<p>
						3 名称: <input type="text" size="10" name="sub_button_0_2"> &nbsp;
							<input type="radio" name="type_0_2" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_0_2" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_0_2" size="30"> 
						</p>


						<p>
						3  名称: <input type="text" size="10" name="sub_button_0_3"> &nbsp;
							<input type="radio" name="type_0_3" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_0_3" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_0_3" size="30"> 
						</p>

						<p>
						5 名称: <input type="text" size="10" name="sub_button_0_4"> &nbsp;
							<input type="radio" name="type_0_4" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_0_4" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_0_4" size="30"> 
						</p>
					</td>
				</tr>
				<tr>
					<th>菜单二:</th>
					<td>
						<input type="text" size="10" name="button_1"> <br>
						<input type="radio" name="type_1" checked value="url"> 	链接 <br>
						<input type="radio" name="type_1" value="key"> 模拟关键字<br>
						<input type="text" name="urlkey_1" size="30"> 
					</td>
					<td>
						<p>
						1 名称: <input type="text" size="10" name="sub_button_1_0"> &nbsp;
							<input type="radio" name="type_1_0" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_1_0" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_1_0" size="30"> 
						</p>


						<p>
						2  名称: <input type="text" size="10" name="sub_button_1_1"> &nbsp;
							<input type="radio" name="type_1_1" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_1_1" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_1_1" size="30"> 
						</p>

						<p>
						3 名称: <input type="text" size="10" name="sub_button_1_2"> &nbsp;
							<input type="radio" name="type_1_2" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_1_2" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_1_2" size="30"> 
						</p>


						<p>
						3  名称: <input type="text" size="10" name="sub_button_1_3"> &nbsp;
							<input type="radio" name="type_1_3" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_1_3" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_1_3" size="30"> 
						</p>

						<p>
						5 名称: <input type="text" size="10" name="sub_button_1_4"> &nbsp;
							<input type="radio" name="type_1_4" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_1_4" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_1_4" size="30"> 
						</p>
					</td>
				</tr>
				<tr>
					<th>菜单三:</th>
					<td>
						<input type="text" size="10" name="button_2"> <br>
						<input type="radio" name="type_2" checked value="url"> 	链接 <br>
						<input type="radio" name="type_2" value="key"> 模拟关键字<br>
						<input type="text" name="urlkey_2" size="30"> 
					</td>
					<td>
						<p>
						1 名称: <input type="text" size="10" name="sub_button_2_0"> &nbsp;
							<input type="radio" name="type_2_0" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_2_0" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_2_0" size="30"> 
						</p>


						<p>
						2  名称: <input type="text" size="10" name="sub_button_2_1"> &nbsp;
							<input type="radio" name="type_2_1" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_2_1" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_2_1" size="30"> 
						</p>

						<p>
						3 名称: <input type="text" size="10" name="sub_button_2_2"> &nbsp;
							<input type="radio" name="type_2_2" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_2_2" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_2_2" size="30"> 
						</p>


						<p>
						3  名称: <input type="text" size="10" name="sub_button_2_3"> &nbsp;
							<input type="radio" name="type_2_3" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_2_3" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_2_3" size="30"> 
						</p>

						<p>
						5 名称: <input type="text" size="10" name="sub_button_2_4"> &nbsp;
							<input type="radio" name="type_2_4" checked value="url"> 	链接 &nbsp;  <input type="radio" name="type_2_4" value="key"> 模拟关键字  &nbsp;
							<input type="text" name="urlkey_2_4" size="30"> 
						</p>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center"><br><input type="submit" name="do_submit" value="设置菜单"><br></td>
				</tr>
			</form>
			</table>
			';
		}
		
	}

	function my_json_encode($type, $p)
	{
	    if (PHP_VERSION >= '5.4')
	    {
	        $str = json_encode($p, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	    }
	    else
	    {
	        switch ($type)
	        {
	            case 'text':
	                isset($p['text']['content']) && ($p['text']['content'] = urlencode($p['text']['content']));
	                break;
	        }
	        $str = urldecode(json_encode($p));
	    }
	    return $str;
	}

	//获取access_token
	function get_token() {
			
		$appid="wxb9e913d1b2e3c475";
		$secret="4834897327e888eceeb5b1a0a3ec2af8";

		$json=$this->https_request("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}");

		$arr= json_decode($json, true);

		$access_token = $arr["access_token"];

		return $access_token;
	}

	function https_request($url, $data = null)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}


}
