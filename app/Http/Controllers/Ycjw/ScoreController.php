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
        if (isTestAccount($user->uno)) {
            return  RJM([
                'list' => [],
                'grade_name' => '大一',
                'semester' => '上',
                'term' => '2013/2014(1)',
            ], 1, '获取成绩成功');
        }

        $ext = $user->ext;
        $start_grade = intval(substr($user->uno, 0, 4));

        $term_year_req = intval($request->get('term_year'));
        if ($term_year_req) {
            $semester_req = intval($request->get('term_semester'));
            if ($semester_req) {
                $term = "$term_year_req/".($term_year_req + 1)."($semester_req)";
                error_log($term);
            } else {
                return RJM(null, -1, '参数错误');
            }
        } else {
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
            if($api->getError() == '用户名或密码错误') {
                return RJM(null, -1, '登录正方教务失败');
            }
            return RJM(null, -1, $api->getError());
        }

        $term_grade = intval(substr($term, 0, 4));
        $semester_grade = intval(substr($term, 10, 1));

        $grade_list = ['一', '二', '三', '四', '五', '六'];
        $semester_list = ['上', '下', '短'];

        if(isset($grade_list[$term_grade - $start_grade])) {
            $grade_name = $grade_list[$term_grade - $start_grade];
        } else {
            $grade_name = '？';
        }

        if(isset($semester_list[$semester_grade - 1])) {
            $semester = $semester_list[$semester_grade - 1];
        } else {
            $semester = '？';
        }

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

        $ext = $user->ext;
        $start_grade = intval(substr($user->uno, 0, 4));

        $term_year_req = intval($request->get('term_year'));
        if ($term_year_req) {
            $semester_req = intval($request->get('term_semester'));
            if ($semester_req) {
                $term = "$term_year_req/".($term_year_req + 1)."($semester_req)";
                error_log($term);
            } else {
                return RJM(null, -1, '参数错误');
            } 
        } else {
            $term = $ext['terms']['score_term'];
            preg_match_all('/\d+/', $term, $pregResult);
            $year = intval($pregResult[0][0]);
            if ($start_grade <= 2013) {
                return RJM(null, -1, '2013级以前学生无法使用明细功能');
            } else if ($year <= 2016) {
                $term = '2017/2018(1)';
                $user->setExt('terms.score_term', $term);
            }
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
            if($api->getError() == '用户名或密码错误') {
                return RJM(null, -1, '登录正方教务失败');
            }
            return RJM(null, -1, $api->getError());
        }

        $term_grade = intval(substr($term, 0, 4));
        $semester_grade = intval(substr($term, 10, 1));

        $grade_list = ['一', '二', '三', '四', '五', '六'];
        $semester_list = ['上', '下', '短'];

        if(isset($grade_list[$term_grade - $start_grade])) {
            $grade_name = $grade_list[$term_grade - $start_grade];
        } else {
            $grade_name = '？';
        }

        if(isset($semester_list[$semester_grade - 1])) {
            $semester = $semester_list[$semester_grade - 1];
        } else {
            $semester = '？';
        }

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
