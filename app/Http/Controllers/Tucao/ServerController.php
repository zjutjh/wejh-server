<?php
namespace App\Http\Controllers\Tucao;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendTemplateMessage;

class ServerController extends Controller
{
    /**
     * 处理吐个槽的webhook消息
     *
     * @return string
     */
    public function serve(Request $request)
    {
        $type = $request->input('input');
        $payload = $request->input('payload');
        switch ($type) {
            case "post.created":
                // 业务代码
                $this->sendMessage('有一个新的留言');
                return RJM(null, 1);
                break;
            case "post.updated":
                // 业务代码
                $this->sendMessage('有一个留言被更新了');
                return RJM(null, 1);
                break;
            case "reply.created":
                // 业务代码
                $this->sendMessage('有一个新的回复');
                return RJM(null, 1);
                break;
            case "reply.updated":
                // 业务代码
                $this->sendMessage('有一个回复被更新了');
                return RJM(null, 1);
                break;
            default:
                return response('400', 400);
                break;
        }
    }

    public function sendMessage($title, $payload) {
        $user_list = ['oIRN_twXj9BH2s5tRWc3oeAdHnBk'];
        foreach ($user_list as $key => $value) {
            $userId = $value;
            $templateId = 'zuvMsJbwiabXHJF7_bt9a7lYZDZxzpPT8tYl4WsuJHU'; // 模板消息id
            $url = '';
            $data = array(
                "first"  => $title,
                "keyword1"   => $payload['user']['username'],
                "keyword2"  => date('Y-m-d'),
                "remark" => "\n" . $payload['post']['content'],
            );
            $job = new SendTemplateMessage($userId, $templateId, $url, $data);
            dispatch($job);
        }
    }
}