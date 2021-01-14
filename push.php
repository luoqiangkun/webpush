<?php
include_once "auth.php";
try {
    //验证签名
    $Auth = new Auth();
    $Auth->checkSignature();
    //加载配置文件
    $config = include_once __DIR__."/config.php";
    // 建立socket连接到内部推送端口
    $client = stream_socket_client($config['tcp'], $errno, $errmsg, 1);
    // 推送的数据，包含uid字段，表示是给这个uid推送
    $data = $_POST['postData'];
    file_put_contents("log.txt",$data);
    // 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
    fwrite($client, $data."\n");
    // 读取推送结果
    echo fread($client, 8192);

} catch (Exception $e) {
    $msg  = $e->getMessage();
    $code = $e->getCode();
    $result = [
        'msg' => $msg,
        'code' => $code,
        'data' => []
    ];
    echo json_encode($result);
}


