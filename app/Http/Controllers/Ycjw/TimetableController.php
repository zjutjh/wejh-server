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

        $start_grade = intval(substr($user->uno, 0, 4));
        preg_match_all('/\d+/', $term, $pregResult);
        $year = intval($pregResult[0][0]);
        if ($start_grade <= 2013 && $year > 2016) {
            $term = '2016/2017(2)';
            $user->setExt('terms.class_term', $term);
        } else if ($start_grade >= 2017 && $year <= 2016) {
            $term = '2017/2018(1)';
            $user->setExt('terms.class_term', $term);
        }
        $api = new Api();

        $class_result = $api->getUEASData('class', $user->uno, [
            'yc' => $ext['passwords']['yc_password'] ? decrypt($ext['passwords']['yc_password']) : '',
            'zf' => $ext['passwords']['zf_password'] ? decrypt($ext['passwords']['zf_password']) : '',
        ], $term, null, true, null);

        if(!$class_result) {
            if($api->getError() == '用户名或密码为空') {
                return RJM(null, -1, '需要绑定');
            }
            if($api->getError() == '用户名或密码错误') {
                return RJM(null, -1, '请重新绑定正方账号');
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
