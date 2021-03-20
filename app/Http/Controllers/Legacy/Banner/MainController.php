<?php

namespace App\Http\Controllers\Legacy\Banner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function banner(Request $request) {
        $link = 'https://wejh.craim.net/weixincard/MzI0MTUzNzQzMg';
        $img = url('/img/banner/0.png');
        $res = [
            'show' => true,
            'router' => false,
            'img' => $img,
            'title' => '精弘网络团队',
            'link' => $link,
            'linkDescribe' => '查看更多'
        ];
        return RJM($res, 1, 'ok');
    }
}
