<?php

namespace App\Http\Controllers\NewYear;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Message\News;

class MainController extends Controller
{
    public function ranking(Request $request) {
        $list = Redis::zrevrange('newYear:ranking', 0, 50, 'withscores');
        if (!$list || empty($list)) {
            return view('ranking' , [
                'list' => json_encode([])
            ]);
        }
        $openids = array_keys($list);
        $app = app('wechat');
        $userService = $app->user;
        $users = $userService->batchGet($openids);
        $user_info_list = $users['user_info_list'];

        foreach ($user_info_list as $key => $value) {
            $openid = $value['openid'];
            if (isset($list[$openid]) && !empty($value['openid'])) {
                $user_info_list[$key]['score'] = $list[$openid];
            }
        }
        usort($user_info_list, function ($a, $b) {
            return $b['score'] - $a['score'];
        });
        return view('ranking' , [
            'list' => json_encode($user_info_list)
        ]);
    }

    public function num(Request $request) {
        $list = Redis::zrevrange('newYear:ranking', 0, 10000, 'withscores');
        return Redis::get('newYear:floor:num') . ',' . sizeof($list);
    }
}

