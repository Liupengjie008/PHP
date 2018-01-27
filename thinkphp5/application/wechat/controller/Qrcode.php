<?php
namespace app\wechat\controller;

//生成二维码
class Qrcode
{
    public function index(){	

    	$Index = new Index();

    	$access_token = $Index->get_token();
    	$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";
    	$data = '{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}';

    	$result = $Index->https_request($url,$data);
    	$result = json_decode($result,true);

  //   	array(3) {
		//   ["ticket"] => string(96) "gQEs8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyQUo3TGxnWFZicC0xcC1iTzFxMUkAAgT_UGlaAwSAOgkA"
		//   ["expire_seconds"] => int(604800)
		//   ["url"] => string(45) "http://weixin.qq.com/q/02AJ7LlgXVbp-1p-bO1q1I"
		// }
    	$qrcode_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($result['ticket']);
    	// echo $qrcode_url;
		
		echo '<img src="'.$qrcode_url.'"  alt="二维码" />'.'<br/>';
		echo '<a href="'.$qrcode_url.'" download="下载二维码">下载二维码</a>';



		echo '<br/>';

		// 生成短链接
		$short_url = $this->short_url($access_token,$qrcode_url);
		echo '二维码短链接：'.$short_url;

    }

    // 生成短链接
    function short_url($access_token,$long_url){
    	$url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token={$access_token}";
    	$data = '{"action":"long2short","long_url":"'.$long_url.'"}';
    	$Index = new Index();
    	$result = $Index->https_request($url,$data);
    	$result = json_decode($result);
    	return $result->short_url;
    }

}

