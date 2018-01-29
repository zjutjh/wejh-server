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
        $start_grade = intval(substr($user->uno, 0, 4));
        $ext = $user->ext;
        $term = $ext['terms']['score_term'];
        preg_match_all('/\d+/', $term, $pregResult);
        $year = intval($pregResult[0][0]);
        if ($start_grade <= 2013 && $year > 2016) {
            $term = '2016/2017(2)';
            $user->setExt('terms.score_term', $term);
        } else if ($start_grade >= 2017 && $year <= 2016) {
            $term = '2017/2018(1)';
            $user->setExt('terms.score_term', $term);
        } else if ($year <= 2016 && $user->uno == '201603090210') {
            $term = '2017/2018(1)';
            $user->setExt('terms.score_term', $term);
        }

        $api = new Api;
        $score_result = $api->getUEASData('score', $user->uno, [
            'yc' => $ext['passwords']['yc_password'] ? decrypt($ext['passwords']['yc_password']) : '',
            'zf' => $ext['passwords']['zf_password'] ? decrypt($ext['passwords']['zf_password']) : ''
        ], $term, null, true, null);
        if(!$score_result) {
            if($api->getError() == '用户名或密码为空') {
                return RJM(null, -1, '需要绑定');
            }
            return RJM(null, -1, $api->getError());
        }

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


    public function detail(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $start_grade = intval(substr($user->uno, 0, 4));
        $ext = $user->ext;
        $term = $ext['terms']['score_term'];
        preg_match_all('/\d+/', $term, $pregResult);
        $year = intval($pregResult[0][0]);
        if ($start_grade <= 2013) {
            return RJM(null, -1, '2013级以前学生无法使用明细功能');
        } else if ($year <= 2016) {
            $term = '2017/2018(1)';
            $user->setExt('terms.score_term', $term);
        }
        $api = new Api;
        $score_result = $api->getUEASData('scoreDetail', $user->uno, [
            'yc' => $ext['passwords']['yc_password'] ? decrypt($ext['passwords']['yc_password']) : '',
            'zf' => $ext['passwords']['zf_password'] ? decrypt($ext['passwords']['zf_password']) : ''
        ], $term, null, true, null);
        if(!$score_result) {
            if($api->getError() == '用户名或密码为空') {
                return RJM(null, -1, '需要绑定');
            }
            return RJM(null, -1, $api->getError());
        }

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
}
