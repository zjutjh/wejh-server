<?php

namespace App\Http\Controllers\Ycjw;

use App\Models\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FreeroomController extends Controller
{
    public function api(Request $request) {
        $area = $request->get('area');
        $startTime  = $request->get('startTime');
        $endTime  = $request->get('endTime');
        $weekday  = $request->get('weekday');
        $week  = $request->get('week');
        if(!$area or !$startTime or !$endTime or !$weekday or !$week) {
            return RJM(null, -1, '缺少必要参数');
        }
        $term = config('system.current_term');
        $api = new Api();
        $freeroomData = $api->getFreeRoom($term, $area, $startTime, $endTime, $weekday, $week);
        $errmsg = $api->getError();
        if($errmsg) {
            return RJM(null, -1, $errmsg);
        }

        return RJM($freeroomData, 1, null);
    }
}
