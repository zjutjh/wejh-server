<?php
namespace App\Http\Controllers\Wechat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Jobs\SendTemplateMessage;

class NotificationController extends Controller
{
    public function boomerang(Request $request)
    {
        $userId = $request->get('openid');
        $templateId = '1l8gWm3O0IGbWIFlaCo9K5c7q-U_-2MFBE-7I1xY-Is'; // 模板消息id
        $url = $request->get('url');
        $data = $request->get('data');
        if (!$userId || !$data) {
            return RJM(null, -1, 'need openid and data');
        }
        $job = new SendTemplateMessage($userId, $templateId, $url, $data);
        dispatch($job);
        return RJM(null, 200, 'success');
    }

    public function walk(Request $request)
    {
        $userId = $request->get('openid');
        $templateId = 'QMF8tbqP3FIBZk4_dD2HhiBC0HaIlkwCjTylPqYa9As'; // 模板消息id
        $url = $request->get('url');
        $data = $request->get('data');
        if (!$userId || !$data) {
            return RJM(null, -1, 'need openid and data');
        }
        $job = new SendTemplateMessage($userId, $templateId, $url, $data);
        dispatch($job);
        return RJM(null, 200, 'success');
    }
}