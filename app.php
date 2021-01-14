<?php
//模拟APP服务端发送请求
$url = 'http://localhost/websocket/push.php';
$header = "Content-type: text/xml";
$data = [
    'push' => [
        'uid' => 10001,
        'msg' => "hello luo"
    ]
];
$ch = curl_init(); //初始化curl
curl_setopt($ch, CURLOPT_URL, $url);//设置链接
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头
curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));//POST数据
$response = curl_exec($ch);//接收返回信息
if(curl_errno($ch)){//出错则显示错误信息
    print curl_error($ch);
}
curl_close($ch); //关闭curl链接
