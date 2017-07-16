<?php

namespace App\Http\Controllers\Ycjw;

use App\Models\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    public function score(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $api = $user->ext;
        $yc_password = $api['passwords']['yc_password'] ? decrypt($api['passwords']['yc_password']) : '';
        if(!$yc_password) {
            return RJM(null, -1, '需要绑定');
        }
        $term = $api['terms']['score_term'];
        list($score_result, $errmsg) = $this->getScore($user->uno, $yc_password, $term, null, true);
        if($errmsg) {
            return RJM(null, -1, $errmsg);
        }

        $start_grade = intval(substr($user->uno, 0, 4));
        $term_grade = intval(substr($term, 0, 4));
        $grade_list = ['一', '二', '三', '四'];

        if(isset($grade_list[$term_grade - $start_grade])) {
            $grade_name = $grade_list[$term_grade - $start_grade];
        } else {
            $grade_name = '？';
        }

        $semester = substr($term, 10, 1) == 1 ? '上' : '下';
        $arr = [
            'grade_name' => $grade_name,
            'semester' => $semester,
            'term' => $term,
        ];
        $res = array_merge($arr, $score_result);
        return RJM($res, 1, '获取成绩成功');
    }

    public function update(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $term = $request->get('term');
        $user->setExt('terms.score_term', $term);

        return RJM(null, 1, '切换学期成功');
    }
    public function getScore($username, $password, $term, $port = null, $retry = false, $timeout = 0.5) {
        $api = new Api;
        $score_result = $api->getYcScore($username, $password, $term, $port, $timeout);
        if(!is_array($score_result) && !$retry) {
            if($api->getError() == '原创服务器错误') {
                return [null, '原创教务系统炸了'];
            }
            return [null, $api->getError()];
        }
        if(!is_array($score_result) && $retry) {
            for ($i = 83; $i <= 86; $i++) {
                $score_result = $api->getYcScore($username, $password, $term, $i, $timeout);
                if(is_array($score_result)) {
                    break;
                }
            }
            if(!is_array($score_result)) {
                if($api->getError() == '原创服务器错误') {
                    return [null, '原创教务系统炸了'];
                }
                return [null, $api->getError()];
            }
        }
        return [$score_result, ''];
    }
}
