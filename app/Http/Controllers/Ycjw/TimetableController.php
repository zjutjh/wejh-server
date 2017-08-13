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
        $yc_password = $ext['passwords']['yc_password'] ? decrypt($ext['passwords']['yc_password']) : '';
        $term = $ext['terms']['class_term'];
        if(!$yc_password) {
            return RJM(null, -1, '需要绑定');
        }

        list($class_result, $errmsg) = $this->getTimetable($user->uno, $yc_password, $term, null, true);

        if($errmsg) {
            return RJM(null, -1, $errmsg);
        }

        $class_result['term'] = $term;

        return RJM($class_result, 1, '获取课表成功');
    }

    public function old(Request $request)
    {
        $uno = $request->get('id');
        if(!$uno) {
            if(Auth::guest()) {
                return RJM(null, -1, '没有认证信息');
            }
            $user = Auth::user();
        } else {
            $user = User::where('uno', $uno)->first();
        }

        if(!$user) {
            return RJM(null, -1, '找不到用户');
        }

        $ext = $user->ext;

        $yc_password = $ext['passwords']['yc_password']?decrypt($ext['passwords']['yc_password']):'';

        if(!$term = $request->get('term')) {
            $term = $ext['terms']['class_term'];
        }

        if(!$yc_password || !$user->uno) {
            return RJM(null, -1, '需要绑定');
        }

        list($class_result, $errmsg) = $this->getTimetable($user->uno, $yc_password, $term, null, true);

        if($errmsg) {
            return RJM(null, -1, $errmsg);
        }

        return '现在是'.date('Y-m-d H:i:s')."<br>课表信息".$class_result['pending_time']."秒前来自原创教务管理系统<br><br>".$this->makeClassTable($class_result['class_list']);
    }

    public function makeClassTable($class)
    {
        $style = '
    	<meta content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    	<style type="text/css">
    	table {
	         font-size: 10px;
	         text-align: center;
	         color: #333333;
	         height: 50%;
	         width: 100%;
	         min-height: 450px;
	         border-spacing: 0px;
	         margin: 0 auto;
	    }
	    td {
	        border: 1px solid #c4c4c4;
	    }
	    tr:first-child {
	    	background-color: #dedede;
	    }
	    </style>';
        $table = $style.'<table><tbody><tr><td><b>节</b></td><td><b>周一</b></td><td><b>周二</b></td><td><b>周三</b></td><td><b>周四</b></td><td><b>周五</b></td><td><b>周六</b></td><td><b>周日</b></td></tr>';
        $day = array();
        for ($i=0; $i < 7 ; $i++)
        {
            $day[$i] = 0;
        }
        for($jie=2;$jie<14;$jie++)
        {
            $table .="<tr>";
            $table .= "<td><span id=\"DG_GXK__ctl".$jie."_LblXiaoQU\">".($jie-1)."</span></td>";
            for($zhou=1;$zhou<8;$zhou++)
            {
                if($day[$zhou-1] < 0)
                {
                    $day[$zhou-1]++;
                    continue;
                }
                $find = false;
                $rowspan = 0;
                $course = array();
                foreach ($class as $k => $val)
                {
                    if($find == false)
                    {
                        foreach ($val['信息'] as $key => $value)
                        {
                            if($value['开始节']==$jie-1 && $value['星期']==$zhou)
                            {
                                array_push($course, ($val['名称']."/(".$value['周'].")|".$value['地点']."/".$val['老师']));
                                $rowspan = ($value['结束节']-$value['开始节']+1);
                                /*$table.="<td rowspan=".($value['结束节']-$value['开始节']+1)."><span id=\"DG_GXK__ctl".$jie."_XQ".$zhou."\">";
                                $table.= $val['名称']."/(".$value['周'].")|".$value['地点']."/".$val['老师'];
                                $day[$zhou-1] -= ($value['结束节']-$value['开始节']);
                                $find = true;
                                break;*/
                            }
                        }
                    }
                }
                if($rowspan==0)
                {
                    $table.="<td><span id=\"DG_GXK__ctl".$jie."_XQ".$zhou."\">";
                }
                else
                {
                    $table.="<td rowspan=".$rowspan."><span id=\"DG_GXK__ctl".$jie."_XQ".$zhou."\">";
                    foreach ($course as $key => $value) {
                        $table.= $value."<br>";
                    }
                    $day[$zhou-1] -= $rowspan-1;
                }
                $table.="</span></td>";
            }
            $table.="</tr>";
        }
        $table .= "</tbody></table>";
        return $table;
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

    public function getTimetable($username, $password, $term, $port = null, $retry = false, $timeout = 300) {
        $api = new Api;
        $class_result = $api->getYcClass($username, $password, $term, $port, $timeout);
        if(!is_array($class_result) && !$retry) {
            if($api->getError() == '原创服务器错误') {
                return [null, '原创教务系统炸了'];
            }
            return [null, $api->getError()];
        }
        if(!is_array($class_result) && $retry) {
            for ($i = 83; $i <= 86; $i++) {
                $class_result = $api->getYcClass($username, $password, $term, $i, $timeout);
                if(is_array($class_result)) {
                    break;
                }
            }
            if(!is_array($class_result)) {
                if($api->getError() == '原创服务器错误') {
                    return [null, '原创教务系统炸了'];
                }
                return [null, $api->getError()];
            }
        }
        return [$class_result, ''];
    }

    public function fixTimetable($class_result) {
        $class_list = $class_result['class_list'];
        $lessons = [];
        for ($i = 0; $i < 7; $i++) {
            $lessons[$i] = [];
            for ($j=0; $j < 12; $j++) {
                $lessons[$i][$j] = [];
            }
        }
        foreach ($class_list as $key => $val) {
            foreach ($val['信息'] as $k => $v) {
                $lesson = [];
                $lesson['class_id'] = $key + 1;
                $lesson['weeks'] = [];
                $lesson['name'] = $val['名称'];
                $lesson['teacher'] = $val['老师'];
                $lesson['type'] = $val['课程类型'];
                $lesson['place'] = $v['地点'];
                $lesson['number'] = $v['结束节'] - $v['开始节'] + 1;
                //$lesson['all_week'] = $v['结束周'] - $v['开始周'] + 1;
                $lesson['all_week'] = $v['开始周'] . '-' . $v['结束周'];
                for($i = $v['开始周']; $i <= $v['结束周']; $i++) {
                    //$lesson['weeks'][$i] = true;
                    array_push($lesson['weeks'], intval($i));
                }
                /*if($v['开始节'] < 5) {
                    $jie = $v['开始节'] / 2;
                } else {
                    $jie = ($v['开始节'] - 1) / 2;
                }*/
                $jie = $v['开始节'] - 1;
                array_push($lessons[intval($v['星期'])-1][$jie], $lesson);
            }
        }
        return $lessons;
    }
}
