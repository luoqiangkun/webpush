<?php
class App{
    const TOKEN = "ydsyiet3759235235";
    private $apiUrl = "http://localhost/websocket/push.php";

    //随机生成字符串
    private function createNonceStr($length = 8) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return "z".$str;
    }

    /**
     * @param $postData  请求数据
     * @param $timeStamp 时间戳
     * @param $nonceStr 随机字符串
     * @return string 返回签名
     */
    private function createSignature($postData,$timeStamp,$nonceStr){
        $arr['postData']  = json_encode($postData);
        $arr['timeStamp'] = $timeStamp;
        $arr['nonceStr']  = $nonceStr;
        $arr['token']     = self::TOKEN;
        //按照首字母大小写顺序排序
        sort($arr,SORT_STRING);
        //拼接成字符串
        $str = implode($arr);
        //进行加密
        $signature = sha1($str);
        $signature = md5($signature);
        //转换成大写
        $signature = strtoupper($signature);
        return $signature;
    }

    public function request($url,$data){
        $ch = curl_init(); //初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//设置链接
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
       // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头
        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));//POST数据
        $response = curl_exec($ch);//接收返回信息
        if(curl_errno($ch)){//出错则显示错误信息
            print curl_error($ch);
        }
        curl_close($ch); //关闭curl链接
        return $response;
    }

    public function run(){
        $postData = [
            'uid' => 10001,
            'msg' => "hello luo"
        ];

        $timeStamp = date('Y-m-d H:i:s');
        $nonceStr = $this->createNonceStr();
        $sign = $this->createSignature($postData,$timeStamp,$nonceStr);
        $params = [
            'postData' => json_encode($postData),
            'timeStamp' => $timeStamp,
            'token' => self::TOKEN,
            'nonceStr' => $nonceStr,
            'sign' => $sign
        ];
        $result = $this->request($this->apiUrl,$params);
        print_r($result);
    }
}

$App = new App();
$App->run();