<?php

namespace App\Http\Controllers\Legacy\Tip;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function tip(Request $request) {
        $message = '精弘十五周年，感谢有你（面向朝晖）';
        $link = 'http://mp.weixin.qq.com/s/ntWojtX_TTChHndBWKb2Zw';
        $res = [
            'msg' => $message,
            'link' => $link
        ];
        return RJM($res, 1, 'ok');
    }
}
