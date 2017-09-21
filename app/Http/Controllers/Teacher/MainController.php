<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function search(Request $request) {
        $wd = $request->get('wd');
        if (!$wd) {
            return RJM([], 1, 'ok');
        }
        $list = Teacher::where('name', 'like', "%$wd%")->get();

        $res = [
            'wd' => $wd,
            'list' => []
        ];

        foreach ($list as $key => $value) {
            $t = [];
            $t['name'] = $value['name'];
            $t['office_phone'] = $value['office_phone'];
            $t['email'] = $value['email'];

            array_push($res['list'], $t);
        }

        return RJM($res, 1, 'ok');
    }
}
