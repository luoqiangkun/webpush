<?php
class Auth{
    const TOKEN = "ydsyiet3759235235";
    /**
     * @param $postData  请求数据
     * @param $timeStamp 时间戳
     * @param $nonceStr 随机字符串
     * @return string 返回签名
     */
    private function createSignature($postData,$timeStamp,$nonceStr){
        $arr['postData']  = $postData;
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

    /**
     * @return void 验证签名
     */
    public function checkSignature(){
        $postData  = $_POST['postData'];//请求数据
        $timeStamp = $_POST['timeStamp']; //时间戳
        $nonceStr  = $_POST['nonceStr']; //随机字符串
        $token     = $_POST['token'];//TOKEN
        $signature = $_POST['sign'];//签名
        if(!$timeStamp || !$nonceStr || !$token || !$signature){
            throw new Exception("请求参数错误",4001);
        }
        if($token !== self::TOKEN){
            throw new Exception("请求token错误",4002);
        }
        if($timeStamp < date("Y-m-d H:i:s",time() - 180)){
            throw new Exception("请求已失效",4003);
        }

        $sign = $this->createSignature($postData,$timeStamp,$nonceStr);
        if($signature != $sign){
            throw new Exception("签名错误",4004);
        }
        echo "签名验证成功";
    }
}