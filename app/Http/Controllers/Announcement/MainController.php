<?php

namespace App\Http\Controllers\Announcement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function api(Request $request) {
        $title = '微精弘上线了';
        $content = '<p>微精弘终于上线了，有任何问题，请加QQ群:462530805</p>';
        $show = true;
        $res = [
            'show' => $show,
            'title' => $title,
            'content' => $content
        ];
        return RJM($res, 1, 'ok');
    }
}
