<?php

namespace App\Http\Controllers\Ycjw;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TimeController extends Controller
{
    public function api(Request $request) {
        date_default_timezone_set("Asia/Shanghai");
        $day = intval(date("w"));
        if($day == 0) {
            $day = 7;
        }
        $current_term = config('system.current_term');
        $term_start_date = config('system.term_start_date');
        $week = $this->getWeek($term_start_date);
        $time = [
            'term' => $current_term,
            'month' => date('m'),
            'week' => $week,
            'day' => $day,
            'is_begin' => $week > 0,
        ];
        return RJM($time, 1, '获取时间成功');
    }

    public function getWeek($start) {
        $now = date('Y-m-d');
        $start_time = strtotime($start);
        $now_time = strtotime($now);
        $between = ($now_time - $start_time) / 3600 / 24 / 7;
        if($between < 0) {
            return 0;
        }
        return intval($between + 1);
    }
}
