<?php

namespace App\Http\Controllers\Announcement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function api(Request $request) {
        $title = '喜迎十九大';
        $content = '
<p>经过精弘人员的努力，服务正常时间调整为6:00 ~ 24:00</p>
<p>感谢你对精弘的支持</p>
<p>有任何问题，请加QQ群:462530805</p>
';
        $show = true;
        $res = [
            'id' => 2,
            'show' => $show,
            'title' => $title,
            'content' => $content
        ];
        return RJM($res, 1, 'ok');
    }
}
