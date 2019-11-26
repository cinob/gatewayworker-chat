<?php
namespace app\index\controller;

use think\facade\Request;
use app\common\model\Workerman;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch('Index/index');
    }

    public function ws()
    {
    	$client_id = Request::param('cid');
        $uid = Request::param('uid');
    	$touid = Request::param('touid');
    	$message = Request::param('message', '你好');
        $ws = new Workerman;
        if ($client_id) {
            $ws->bindUid($client_id, $uid);
        }
        $ws->sendToSingle($touid, '用户' . $uid . ' : ' .$message);
        return '发送成功';
    }
}
