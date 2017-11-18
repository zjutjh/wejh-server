<?php

namespace App\Http\Controllers\Ycjw;

use App\Models\Api;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    public function exam(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $ext = $user->ext;
        $yc_password = $ext['passwords']['yc_password'] ? decrypt($ext['passwords']['yc_password']) : '';
        $term = $ext['terms']['exam_term'];

        $start_grade = intval(substr($user->uno, 0, 4));
        preg_match_all('/\d+/', $term, $pregResult);
        $year = intval($pregResult[0][0]);
        if ($start_grade <= 2013 && $year > 2016) {
            $term = '2016/2017(2)';
            $user->setExt('terms.exam_term', $term);
        }
        $api = new Api;
        $exam_result = $api->getUEASData('exam', $user->uno, [
            'yc' => $ext['passwords']['yc_password'] ? decrypt($ext['passwords']['yc_password']) : '',
            'zf' => $ext['passwords']['zf_password'] ? decrypt($ext['passwords']['zf_password']) : ''
        ], $term, null, true, 1200);

        if(!$exam_result) {
            if($api->getError() == '用户名或密码为空') {
                return RJM(null, -1, '需要绑定');
            }
            return RJM(null, -1, $api->getError());
        }

        $exam_result['term'] = $term;

        return RJM($exam_result, 1, '获取排考成功');
    }

    public function update(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $term = $request->get('term');
        $user->setExt('terms.exam_term', $term);

        return RJM(null, 1, '切换学期成功');
    }

//    public function getExam($username, $password, $term, $port = null, $retry = false, $timeout = 900) {
//        $api = new Api;
//        $exam_result = $api->getYcExam($username, $password, $term, $port, $timeout);
//        if(!is_array($exam_result) && !$retry) {
//            if($api->getError() == '原创服务器错误') {
//                return [null, '原创教务系统炸了'];
//            }
//            return [null, $api->getError()];
//        }
//        if(!is_array($exam_result) && $retry) {
//            for ($i = 83; $i <= 86; $i++) {
//                $exam_result = $api->getYcExam($username, $password, $term, $i, $timeout);
//                if(is_array($exam_result)) {
//                    break;
//                }
//            }
//            if(!is_array($exam_result)) {
//                if($api->getError() == '原创服务器错误') {
//                    return [null, '原创教务系统炸了'];
//                }
//                return [null, $api->getError()];
//            }
//        }
//
//        return [$exam_result, ''];
//    }
}
