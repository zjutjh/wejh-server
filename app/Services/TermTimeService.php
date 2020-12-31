<?php
namespace App\Services;

class TermTimeService {

    public static function getTermTime()
    {
        // date_default_timezone_set("Asia/Shanghai");
        $day = intval(date("w"));
        if($day == 0) {
            $day = 7;
        }
        $current_term = config('system.current_term');
        $term_start_date = config('system.term_start_date');
        $week = self::getWeek($term_start_date);
        return [
            'term' => $current_term,
            'month' => date('m'),
            'week' => $week,
            'day' => $day,
            'is_begin' => $week > 0,
        ]; 
    }

    public static function getWeek($start) {
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
