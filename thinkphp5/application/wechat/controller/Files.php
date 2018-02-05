<?php
namespace app\wechat\controller;

use think\Controller;
use think\Cache;
use think\Db;
use think\Url;
use think\Request;

/**
* 
*/
class Files extends Controller
{
	
	public function index(){

		new wechat();
		$request = Request::instance();
		// 获取当前域名
		echo 'domain: ' . $request->domain() . '<br/>';
		// 获取当前入口文件
		echo 'file: ' . $request->baseFile() . '<br/>';
		// 获取当前URL地址 不含域名
		echo 'url: ' . $request->url() . '<br/>';
		// 获取包含域名的完整URL地址
		echo 'url with domain: ' . $request->url(true) . '<br/>';
		// 获取当前URL地址 不含QUERY_STRING
		echo 'url without query: ' . $request->baseUrl() . '<br/>';
		// 获取URL访问的ROOT地址
		echo 'root:' . $request->root() . '<br/>';
		// 获取URL访问的ROOT地址
		echo 'root with domain: ' . $request->root(true) . '<br/>';
		// 获取URL地址中的PATH_INFO信息
		echo 'pathinfo: ' . $request->pathinfo() . '<br/>';
		// 获取URL地址中的PATH_INFO信息 不含后缀
		echo 'pathinfo: ' . $request->path() . '<br/>';
		// 获取URL地址中的后缀信息
		echo 'ext: ' . $request->ext() . '<br/>';
		$info = Db::name('media')->where('status = 1')->select();
		foreach ($info as $k => $v) {
			$info[$k]['url'] = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.ACCESS_TOKEN.'&media_id='.$v['media_id'];
			
			
		}
		$this->assign('info',$info);
		$this->assign('local',$request->domain());
		return $this->fetch();
	}
	//新增临时素材
	public function add(){
		if($_POST){
		    // 获取表单上传文件
		    $files = request()->file();
		    $request = Request::instance();
		    $wechat = new wechat();
		    $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.ACCESS_TOKEN.'&type=image';
		    foreach($files as $k=>$file){
		        // 移动到框架应用根目录/public/uploads/ 目录下
		        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		        if($info){
		            // 成功上传后 获取上传信息
		            // 输出 jpg
			        // echo $info->getExtension().'<br/>';
			        // // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
			        // echo $info->getSaveName().'<br/>';
			        // // 输出 42a79759f284b767dfcb2a0197904287.jpg
			        // echo $info->getFilename().'<br/>'; 
			        // $image[$k] = $request->domain().'/thinkphp5/public/uploads/'.$info->getSaveName();
			        // $image[$k] = str_replace("\\","/",$image[$k]);


					//形成上传的数据，调用公众号接口，上传
					//上传公众号一定要使用绝对路径
			        $image[$k] = 'D:\WWW\thinkphp5\public\uploads\\'.$info->getSaveName();
			        //php版本的问题，低版本不支持curlfile，高版本不支持@+文件路径
					if (class_exists('\CURLFile'))
					{
					    $data = array('media' => new \CURLFile(str_replace('/','\\',$image[$k])));
					}
					else
					{
					    $data = array('media' => '@'.str_replace('/','\\',$image[$k]));
					}
					$result[$k] = $wechat->https_request($url, $data);
					$wechat_info = json_decode($result[$k],true);
					$mysql[$k]['status'] = 1;
					$mysql[$k]['type'] = $wechat_info['type'];
					$mysql[$k]['media_id'] = $wechat_info['media_id'];
					$mysql[$k]['created_at'] = $wechat_info['created_at'];
					$mysql[$k]['local_url'] = str_replace("\\","/",'/thinkphp5/public/uploads/'.$info->getSaveName());

		        }else{
		            // 上传失败获取错误信息
		            echo $file->getError();
		        }    
		    }

		    foreach ($mysql as $k => $v) {
		    	Db::name('media')->insert($v);
		    }

		}else{
			return $this->fetch();
		}
	}

	

	public function wechat_uplode(){
		$wechat = new wechat();
		$url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.ACCESS_TOKEN.'&type=image';
		$server_file = 'D:\WWW\thinkphp5\public\uploads\20180130\0d97fda433e8170f34b1dd526d6fefa4.jpg';
		//php版本的问题，低版本不支持curlfile，高版本不支持@+文件路径
		if (class_exists('\CURLFile'))
		{
		    $data = array('media' => new \CURLFile(str_replace('/','\\',$server_file)));
		}
		else
		{
		    $data = array('media' => '@'.str_replace('/','\\',$server_file));
		}
		$result = $wechat->https_request($url,$data);
		dump($result);
	}



	public function upload(){
	    // 获取表单上传文件
	    $files = request()->file('image');

	    foreach($files as $file){
	        // 移动到框架应用根目录/uploads/ 目录下
	        $info = $file->move( '../uploads');
	        if($info){
	            // 成功上传后 获取上传信息
	            // 输出 jpg
	            echo $info->getExtension(); 
	            // 输出 42a79759f284b767dfcb2a0197904287.jpg
	            echo $info->getFilename(); 
	        }else{
	            // 上传失败获取错误信息
	            echo $file->getError();
	        }    
	    }
	}

}