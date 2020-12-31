<?php

namespace App\Http\Controllers\Ycjw;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TermTimeService;

class TimeController extends Controller
{

    public function api(Request $request) {

        $time = TermTimeService::getTermTime();
        return RJM($time, 1, '获取时间成功');
    }

}
