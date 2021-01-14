<?php
use Workerman\Worker;
//use Workerman\Lib\Timer;
require 'vendor/autoload.php';
// 注意：这里与上个例子不同，使用的是websocket协议
$worker = new Worker("websocket://0.0.0.0:2000");
// 启动1个进程对外提供服务
$worker->count = 1;
$worker->onWorkerStart = function($worker){

    // 开启一个内部端口，方便内部系统推送数据，Text协议格式 文本+换行符
    $innerTextWorker = new Worker('text://0.0.0.0:5678');
    $innerTextWorker->onMessage = function($connection, $buffer) {
        // $data数组格式，里面有uid，表示向那个uid的页面推送数据
        $data = json_decode($buffer, true);
        $uid = $data['uid'];
        // 通过workerman，向uid的页面推送数据
        $ret = sendMessageByUid($uid, $buffer);
        // 返回推送结果
        $connection->send($ret ? 'success' : 'fail');
    };
    // ## 执行监听 ##
    $innerTextWorker->listen();
};
// 新增加一个属性，用来保存uid到connection的映射
$worker->uidConnections = array();
// 当有客户端发来消息时执行的回调函数
$worker->onMessage = function($connection, $data) {
    global $worker;
    // 判断当前客户端是否已经验证,既是否设置了uid
    if(!isset($connection->uid)) {
        // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
        $connection->uid = $data;
        /* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
         * 实现针对特定uid推送数据
         */
        $worker->uidConnections[$connection->uid] = $connection;
        return;
    }
};
// 当有客户端连接断开时
$worker->onClose = function($connection) {
    global $worker;
    if(isset($connection->uid))
    {
        // 连接断开时删除映射
        unset($worker->uidConnections[$connection->uid]);
    }
};
// 向所有验证的用户推送数据
function broadcast($message){
    global $worker;
    foreach($worker->uidConnections as $connection) {
        $connection->send($message);
    }
}
// 针对uid推送数据
function sendMessageByUid($uid, $message){
    global $worker;
    if(isset($worker->uidConnections[$uid])) {
        $connection = $worker->uidConnections[$uid];
        $connection->send($message);
        return true;
    }
    return false;
}
// 运行所有的worker
Worker::runAll();