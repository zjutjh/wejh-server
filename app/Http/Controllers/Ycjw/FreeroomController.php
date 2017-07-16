<?php

namespace App\Http\Controllers\Ycjw;

use App\Models\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FreeroomController extends Controller
{
    public function api(Request $request) {
        $campus = $request->get('campus');
        $startTime  = $request->get('startTime');
        $endTime  = $request->get('endTime');
        $weekday  = $request->get('weekday');
        $week  = $request->get('week');
        if(!$campus or !$startTime or !$endTime or !$weekday or !$week) {
            return RJM(null, -1, '缺少必要参数');
        }
        list($freeroom_list, $errmsg) = $this->getFreeRoom($week, $weekday, $startTime, $endTime, $campus, null, true);
        if($errmsg) {
            return RJM(null, -1, $errmsg);
        }

        return RJM($freeroom_list, 1, null);
    }
    public function getFreeroom($week, $weekday, $startTime, $endTime, $campus, $port = null, $retry = false, $timeout = 0.5) {
        $api = new Api;
        $current_term = config('system.current_term');
        $freeroom_list = $api->getFreeRoom($current_term, $week, $week, $weekday, $startTime, $endTime, $campus, '所有', '所有', 0, $port, $timeout);
        if(!is_array($freeroom_list) && !$retry) {
            if($api->getError() == '原创服务器错误') {
                return [null, '原创教务系统炸了'];
            }
            return [null, $api->getError()];
        }
        if(!is_array($freeroom_list) && $retry) {
            for ($i = 83; $i <= 86; $i++) {
                $freeroom_list = $api->getFreeRoom('2016/2017(1)', $week, $week, $weekday, $startTime, $endTime, $campus, '所有', '所有', 0, $i, $timeout);
                if(is_array($freeroom_list)) {
                    break;
                }
            }
            if(!is_array($freeroom_list)) {
                if($api->getError() == '原创服务器错误') {
                    return [null, '原创教务系统炸了'];
                }
                return [null, $api->getError()];
            }
        }
        return [$freeroom_list, ''];
    }
}
