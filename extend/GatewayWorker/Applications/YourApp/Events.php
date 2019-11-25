<?php
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    private static $secret = 'cinob';

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect(string $client_id): void
    {
        $rand = rand(1111, 9999);
        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, json_encode([
            'cid' => $client_id,
            'status' => $rand,
            'type' => 'login',
            'sign' => hash('sha256', $client_id . $rand . self::$secret)
        ]));
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage(string $client_id, string $message): void
   {
        $message = $message ?: json_decode($message, true);
        if (!$message) {
            return ;
        }

        switch ($message['type']) {
            case 'login':
                # code...
                break;
            
            case 'ping':
            
                break;
        }

        // 向所有人发送
        Gateway::sendToAll($message);
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose(string $client_id): void
   {
        // 向所有人发送 
        GateWay::sendToAll("$client_id logout\r\n");
   }
}
