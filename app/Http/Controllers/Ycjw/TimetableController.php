<?php

namespace App\Http\Controllers\Ycjw;

use App\Models\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TimetableController extends Controller
{
    public function timetable(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $ext = $user->ext;
        $term = $ext['terms']['class_term'];

        $api = new Api();

        $class_result = $api->getUEASData('class', $user->uno, [
            'yc' => $ext['passwords']['yc_password'] ? decrypt($ext['passwords']['yc_password']) : '',
            'zf' => $ext['passwords']['zf_password'] ? decrypt($ext['passwords']['zf_password']) : '',
        ], $term, null, true);

        if(!$class_result) {
            if($api->getError() == '用户名或密码为空') {
                return RJM(null, -1, '需要绑定');
            }
            return RJM(null, -1, $api->getError());
        }

        $class_result['term'] = $term;

        return RJM($class_result, 1, '获取课表成功');
    }

    public function update(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $term = $request->get('term');

        $user->setExt('terms.class_term', $term);

        return RJM([
            'class_term' => $user->getExt('terms.class_term'),
        ], 1, '切换学期成功');
    }
}
