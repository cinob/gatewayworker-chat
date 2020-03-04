<?php
namespace app\index\controller;

use think\facade\Request;
use app\common\model\Workerman;
use think\Controller;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

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

    public function getToken()
    {
        $accessKey = config('qiniu.accessKey');
        $secretKey = config('qiniu.secretKey');
        $bucket = config('qiniu.bucket');
        $auth = new Auth($accessKey, $secretKey);
        // 生成上传Token
        return $auth->uploadToken($bucket);
    }

    public function qiniuUpload()
    {
        $file = Request::file('file');
        $filePath = $file->getInfo('tmp_name');
        $key = md5(microtime(true)).'.' . pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);
        $uploadMgr = new UploadManager;
        list($ret, $err) = $uploadMgr->putFile($this->getToken(), $key, $filePath);
        if ($err !== null) {
            var_dump($err);
        } else {
            return json([
                'key' => config('qiniu.cdn').$ret['key']
            ]);
        }
    }
}
