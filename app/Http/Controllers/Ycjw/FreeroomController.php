<?php

namespace App\Http\Controllers\Ycjw;

use App\Models\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FreeroomController extends Controller
{
    public function api(Request $request) {
        if(!$user = Auth::user()) {
            return RJM(null, -1, '没有认证信息');
        }
        $ext = $user->ext;
        $area = $request->get('area');

        $acturalArea = '';
        // 01: 朝晖, 02: 屏峰, 03: 莫干山
        if ($area == '01' || $area == '02') {
            $acturalArea = $area;
        }  else if ($area == '03') {
            $acturalArea = 'A61400B98155D41AE0550113465EF1CF';
        }

        $startTime  = $request->get('startTime');
        $endTime  = $request->get('endTime');
        $weekday  = $request->get('weekday');
        $week  = $request->get('week');
//        $weekday  = $request->get('week');
//        $week  = $request->get('weekday');
        if(!$area or $startTime == null or $endTime == null or !$weekday or !$week) {
            return RJM(null, -1, '缺少必要参数');
        }
        $term = config('system.current_term');
        $api = new Api();
        $freeroomData = $api->getFreeRoom($user->uno, decrypt($ext['passwords']['zf_password']), $term, $acturalArea, $startTime, $endTime, $weekday, $week);
        $errmsg = $api->getError();
        if($errmsg) {
            return RJM(null, -1, $errmsg);
        }

        return RJM($freeroomData, 1, null);
    }
}
