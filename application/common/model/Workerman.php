<?php
namespace app\common\model;

use GatewayClient\Gateway;

class Workerman 
{

    private $isInit = 0;

    public function __construct(string $addr = '127.0.0.1:1238')
    {
        if (!$this->isInit) {
            Gateway::$registerAddress = $addr;
            $this->isInit = 1;
        }
    }
    
    /**
     * 将client_id与uid绑定
     * @author   cinob
     * @dateTime 2019-11-25
     * @param    string     $client_id Gatewayworker客户端id
     * @param    int        $uid       唯一用户id
     * @return   void
     */
    public function bindUid(string $client_id, int $uid): void
    {
        Gateway::bindUid($client_id, $uid);
    }

    /**
     * 给指定用户发送消息
     * @author   cinob
     * @dateTime 2019-11-25
     * @param    int        $uid     唯一用户id
     * @param    string     $message 消息内容
     * @return   void
     */
    public function sendToSingle(int $uid, string $message): void
    {
        // 判断用户是否在线
        if (Gateway::isUidOnline($uid)) {
            // 推送消息
            Gateway::sendToUid($uid, json_encode([
                'type' => 'msg',
                'info' => $message
            ]));
        }
    }
    
    /**
     * 给指定分组发送消息
     * @author   cinob
     * @dateTime 2019-11-25
     * @param    string     $groupName 分组名
     * @param    string     $message   消息内容
     * @param    array      $exclude   不需要发送信息的用户id
     * @return   void
     */
    public function sendToGroup(string $groupName, string $message, array $exclude = []): void
    {
        // 获取client_id
        if ($exclude) {
            foreach ($exclude as $k => $uid) {
                $exclude[$k] = Gateway::getClientIdByUid($uid);
            }
        }
        // 推送消息
        Gateway::sendToGroup($groupName, json_encode([
            'type' => 'msg',
            'info' => $message
        ]), array_filter($exclude));
    }

}
