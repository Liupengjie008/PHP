<?php
namespace app\wechat\controller;
use think\phpanalysis\phpanalysis;
use think\Db;
use think\Cache;
class wechat
{	

    //构造函数
    function __construct() {
        if(Cache::get('access_token')){
            //声明一个常量定义一个token值
            define("ACCESS_TOKEN", Cache::get('access_token'));
        }else{
            $appid="wxb9e913d1b2e3c475";
            $secret="4834897327e888eceeb5b1a0a3ec2af8";

            $json=$this->https_request("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}");

            $arr= json_decode($json, true);

            $access_token = $arr["access_token"];
            // 使用文件缓存
            Cache::set('access_token',$access_token,3600);
            define("ACCESS_TOKEN", Cache::get('access_token'));
        }
        
    }
	//验证签名
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            //用户发送的消息类型判断
            switch ($RX_TYPE)
            {
                case "text":    //文本消息
                    $result = $this->receiveText($postObj);
                    break;
                case "image":   //图片消息
                    $result = $this->receiveImage($postObj);
                    break;

                case "voice":   //语音消息
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":   //视频消息
                    $result = $this->receiveVideo($postObj);
                    break;
                case "location"://位置消息
                    $result = $this->receiveLocation($postObj);
                    break;
                case "link":    //链接消息
                    $result = $this->receiveLink($postObj);
                    break;
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                default:
                    $result = "unknow msg type: ".$RX_TYPE;
                    break;
            }
            echo $result;
        }else {
            echo "";
            exit;
        }
    }

    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        if($keyword == "文本"){
            //回复文本消息
            $content = "这是个文本消息";
            $result = $this->transmitText($object, $content);
        }
        else if($keyword == "图文" || $keyword == "单图文"){
            //回复单图文消息
            $content = array();
            $content[] = array("Title"=>"单图文标题", 
                                "Description"=>"单图文内容", 
                                "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", 
                                "Url" =>"http://m.cnblogs.com/?u=txw1958");
            $result = $this->transmitNews($object, $content);
        }
        else if($keyword == "多图文"){
            //回复多图文消息
            $content = array();
            $content[] = array("Title"=>"多图文1标题", "Description"=>"", "PicUrl"=>"http://discuz.comli.com/weixin/weather/icon/cartoon.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            $content[] = array("Title"=>"多图文2标题", "Description"=>"", "PicUrl"=>"http://d.hiphotos.bdimg.com/wisegame/pic/item/f3529822720e0cf3ac9f1ada0846f21fbe09aaa3.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            $content[] = array("Title"=>"多图文3标题", "Description"=>"", "PicUrl"=>"http://g.hiphotos.bdimg.com/wisegame/pic/item/18cb0a46f21fbe090d338acc6a600c338644adfd.jpg", "Url" =>"http://m.cnblogs.com/?u=txw1958");
            $result = $this->transmitNews($object, $content);
           
        }
        else if($keyword == "音乐"){
            //回复音乐消息
            $content = array("Title"=>"最炫民族风", 
            "Description"=>"歌手：凤凰传奇", 
            "MusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3",
            "HQMusicUrl"=>"http://121.199.4.61/music/zxmzf.mp3");
            $result = $this->transmitMusic($object, $content);
        }else{
            //调用聊天机器人接口，回复文本消息
            $content = $this->callTuling($keyword);
            $result = $this->transmitText($object, $content);
        }
        
        return $result;
    }

    private function receiveImage($object)
    {
        //回复图片消息 
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);;
        return $result;
    }


    // private function receiveVoice($object)
    // {
    //     //回复语音消息 
    //     $content = array("MediaId"=>$object->MediaId);
    //     $result = $this->transmitVoice($object, $content);;
    //     return $result;
    // }

    //接收语音消息
    private function receiveVoice($object)
    {
        //包含函数库文件， 里面有分词函数 fci()
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $text = $object->Recognition;
            //用回复文本消息  返回分词后的结果
            $content = $this->phpanalysis($text);
            $result = $this->transmitText($object, $content);
                                 
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }



    private function receiveVideo($object)
    {
        //回复视频消息 
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);;
        return $result;
    }  
    
    /*
     * 回复文本消息
     */
    private function transmitText($object, $content)
    {
        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }
    
    /*
     * 回复图片消息
     */
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 回复语音消息
     */
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId>
</Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 回复视频消息
     */
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
</Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }
    
    /*
     * 回复图文消息
     */
    private function transmitNews($object, $arr_item)
    {
        if(!is_array($arr_item))
            return;

        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($arr_item as $item)
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);

        $newsTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<Content><![CDATA[]]></Content>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($arr_item));
        return $result;
    }
    
    /*
     * 回复音乐消息
     */
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $textTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
$item_str
</xml>";

        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

     private function receiveEvent($object)
    {
        $content = "";
        switch ($object->Event)
        {
            case "subscribe":   //关注事件
                /*  请求获取用户基本信息接口，获取用户基本信息
                    存入User数据库
                    FromUserName    发送方帐号（一个OpenID）
                */
                $content = $this->get_userInfo($object->FromUserName);
                break;
            case "unsubscribe": //取消关注事件
                $content = "一路走好";
                break;
            case "LOCATION": //获取用户地理位置
                $content = "你好，我是聊天机器人Murphy，快快开始撩我吧~";
                break;
        }
        $result = $this->transmitText($object, $content);
        return $result;
    }


    //小黄鸡聊天机器人接口
    private function sandbox_api($msg){
        //URL中的参数：
        // sandbox.api.simsimi.com/request.p 是试用账号的API
        // key : 用户秘钥，这里是试用秘钥100次请求/天
        // ft : 是否过滤骂人的词汇
        // lc : 语言设置
        // text : 发送信息
        $msg = strval($msg);
        $url = 'http://sandbox.api.simsimi.com/request.p?key=df3c679b-f20a-4bdc-9592-c8730169fa32&ft=0.0&lc=ch&text='.$msg;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch,CURLOPT_HEADER,0);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 5.1; rv:12.0) Gecko/20120101 Firefox/17.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ;
        $res = curl_exec($ch);
        curl_close($ch);
        return strval($res);
    }

    //创建函数调用图灵机器人接口  
    function callTuling($keyword)  
    {  
        $apiKey = "3515ae7fba2e463c871a9f7f99e7cb0d"; //填写后台提供的key  
        $apiURL = "http://www.tuling123.com/openapi/api?key=KEY&info=INFO";   
      
        $reqInfo = $keyword;   
        $url = str_replace("INFO", $reqInfo, str_replace("KEY", $apiKey, $apiURL));  
        $ch = curl_init();   
        curl_setopt ($ch, CURLOPT_URL, $url);   
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);   
        $file_contents = curl_exec($ch);  
        curl_close($ch);   
        //获取图灵机器人返回的数据，并根据code值的不同获取到不用的数据  
        $message = json_decode($file_contents,true);  
        $result = "";  
        if ($message['code'] == 100000){  
            $result = $message['text'];  
        }else if ($message['code'] == 200000){  
            $text = $message['text'];  
            $url = $message['url'];  
            $result = $text . " " . $url;  
        }else if ($message['code'] == 302000){  
            $text = $message['text'];  
            $url = $message['list'][0]['detailurl'];  
            $result = $text . " " . $url;  
        }else {  
            $result = "好好说话我们还是基佬";  
        }  
        return $result;  
    } 

    //中文分词方法
    private function phpanalysis($text){
        //中文分词

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
                    
        //初始化类
        PhpAnalysis::$loadInit = false;
        $pa = new PhpAnalysis('utf-8', 'utf-8', $pri_dict);
        //载入词典
        $pa->LoadDict();
        //执行分词
        $pa->SetSource($text);
        $pa->differMax = $do_multi;
        $pa->unitWord = $do_unit;
        
        $pa->StartAnalysis( $do_fork );
        //分词结果
        $okresult = $pa->GetFinallyResult(' ', $do_prop);

        return $okresult;
    }

    //获取用户基本信息（包括UnionID机制）
    public function get_userInfo($OpenID){
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".ACCESS_TOKEN."&openid={$OpenID}&lang=zh_CN";
        $userInfo = $this->https_request($url);
        $data = json_decode($userInfo,true);
        $res = Db::name('user')->where("openid = '{$OpenID}'")->find();
        if(!$res){
            Db::name('user')->insert($data);
            $text = '欢迎'.$data['nickname'].'关注刘鹏杰测试号';
        }else{
            $data_update = array(
                'subscribe'     =>$data['subscribe'],
                'nickname'      =>$data['nickname'],
                'sex'           =>$data['sex'],
                'city'          =>$data['city'],
                'country'       =>$data['country'],
                'province'      =>$data['province'],
                'language'      =>$data['language'],
                'headimgurl'    =>$data['headimgurl'],
                'subscribe_time'      =>$data['subscribe_time'],
                'unionid'      =>isset($data['unionid'])?$data['unionid']:'',
                'remark'      =>$data['remark'],
                'groupid'      =>$data['groupid'],
                'tagid_list'      =>$data['tagid_list'],
            );
            Db::name('user')->where("openid = '{$OpenID}'")->update($data_update);
            $text = '哎呀'.$data['nickname'].'，你又浪回来了~';
        }
        return $text;
    }

    public function https_request($url, $data = null)
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

    // 客服接口-发消息
    public function custom_service($openid,$msgtype,$message){
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.ACCESS_TOKEN;
        switch ($msgtype) {
            case 'text'://发送文本消息
                $data['touser'] = $openid;
                $data['msgtype'] = $msgtype;
                $data[$msgtype] = array('content'=>$message);
                $data = json_encode($data);
                $this->https_request($url,$data);
                break;
            case 'image'://发送图片消息
                # code...
                break;
            case 'voice'://发送语音消息
                # code...
                break;
            case 'video'://发送视频消息
                # code...
                break;
            case 'music'://发送音乐消息
                # code...
                break;
            case 'news'://发送图文消息（点击跳转到外链） 图文消息条数限制在8条以内，注意，如果图文数超过8，则将会无响应。
                # code...
                break;
            case 'mpnews'://发送图文消息（点击跳转到图文消息页面） 图文消息条数限制在8条以内，注意，如果图文数超过8，则将会无响应。
                # code...
                break;
            case 'wxcard'://发送卡券
                # code...
                break;
            default://报错自动返回
                return;
                break;
        }

    }



}
